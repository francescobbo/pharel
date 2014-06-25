<?php

namespace Pharel\Visitors;

class ToSql extends Reduce {
    const WHERE = ' WHERE ';
    const SPACE = ' ';
    const COMMA = ', ';
    const GROUP_BY = ' GROUP BY ';
    const ORDER_BY = ' ORDER BY ';
    const WINDOW = ' WINDOW ';
    const _AND = ' AND ';
    const DISTINCT = 'DISTINCT';

    public function __construct($connection) {
        $this->connection     = $connection;
//        $this->schema_cache   = $connection->schema_cache;
        $this->quoted_tables  = [];
        $this->quoted_columns = [];
    }

    public function quoted($o, $a) {
        return $this->quote($o, $this->column_for($a));
    }

    public function quote($value, $column = null) {
        if ($value instanceof Nodes\SqlLiteral)
            return $value;

        return "\"" . addslashes($value) . "\""; //@connection.quote value, column
    }

    public function column_for($attr) {
        if (!$attr)
            return null;

        $name = $attr->name;
        $table = $attr->relation->table_name;

//      if (!$this->table_exists($name))
 //         return null;
        //column_cache(table)[name];
        return null;
    }

    public function visit_Pharel_Nodes_Quoted($o, $collector) {
        return $collector->add($this->quoted($o->expr, null));
    }

    public function visit_Pharel_Nodes_InsertStatement($o, $collector) {
        $collector->add("INSERT INTO ");
        $collector = $this->visit($o->relation, $collector);

        if (count($o->columns)) {
            $cols = array_map(function($x) {
                return $this->quote_column_name($x->name);
            }, $o->columns);
          
            $cols = implode(', ', $cols);
            $collector.add(" ({$cols})");
        }

        if ($o->values)
            return maybe_visit($o->values, $collector);
        else if ($o->select)
            return maybe_visit($o->select, $collector);
        else
            return $collector;
    }

    public function visit_Pharel_Nodes_SelectStatement($o, $collector) {
        if ($o->with) {
            $collector = $this->visit($o->with, $collector);
            $collector.add(self::SPACE);
        }

        $f = function($c, $x) {
            return $this->visit_Arel_Nodes_SelectCore($x, $c);
        };

        foreach ($o->cores as $x) {
            $collector = $f($collector, $x);
        }

        if (!empty($o->orders)) {
            $collector->add(self::SPACE);
            $collector->add(self::ORDER_BY);
            $len = count($o->orders) - 1;

            foreach ($o->orders as $i => $x) {
                $collector = $this->visit($x, $collector);
                if ($i != $len)
                    $collector->add(self::COMMA);
            }
        }

        $collector = $this->maybe_visit($o->limit, $collector);
        $collector = $this->maybe_visit($o->offset, $collector);
        $collector = $this->maybe_visit($o->lock, $collector);

        return $collector;
    }

    public function visit_Arel_Nodes_SelectCore($o, $collector) {
        $collector->add("SELECT");

        if ($o->top) {
            $collector->add(" ");
            $collector = $this->visit($o->top, $collector);
        }

        if ($o->set_quantifier) {
            $collector->add(" ");
            $collector = $this->visit($o->set_quantifier, $collector);
        }


        if (!empty($o->projections)) {
            $collector->add(" ");
            $len = count($o->projections) - 1;
            
            foreach ($o->projections as $i => $x) {
                $collector = $this->visit($x, $collector);
                if ($i != $len)
                    $collector->add(self::COMMA);
            }
        }

        if ($o->source && !empty($o->source)) {
            $collector->add(" FROM ");
            $collector = $this->visit($o->source, $collector);
        }

        if (!empty($o->wheres)) {
            $collector->add(self::WHERE);
            $len = count($o->wheres) - 1;

            foreach ($o->wheres as $i => $x) {
                $collector = $this->visit($x, $collector);
                if ($i != $len)
                    $collector->add(self::_AND);
            }
        }

        if (!empty($o->groups)) {
            $collector->add(self::GROUP_BY);
            $len = count($o->groups) - 1;

            foreach ($o->groups as $i => $x) {
                $collector = $this->visit($x, $collector);
                if ($i != $len)
                    $collector->add(self::COMMA);
            }
        }

        if ($o->having) {
            $collector->add(" ");
            $collector = $this->visit($o->having, $collector);
        }

        if (!empty($o->windows)) {
            $collector->add(self::WINDOW);
            $len = count($o->windows) - 1;
          
            foreach ($o->windows as $i => $x) {
                $collector = $this->visit($x, $collector);
                if ($i != $len)
                    $collector->add(self::COMMA);
            }
        }

        return $collector;
    }

    public function visit_Pharel_Nodes_JoinSource($o, $collector) {
        if ($o->left)
            $collector = $this->visit($o->left, $collector);
        
        if (count($o->right)) {
            if ($o->left)
                $collector->add(" ");
            
            $collector = $this->inject_join($o->right, $collector, ' ');
        }
        
        return $collector;
    }

    public function visit_Pharel_Table($o, $collector) {
        if ($o->table_alias)
            return $collector->add($this->quote_table_name($o->name) . " " . $this->quote_table_name($o->table_alias));
        else
            return $collector->add($this->quote_table_name($o->name));
    }

    public function visit_Pharel_Nodes_Equality($o, $collector) {
        $right = $o->right;

        $collector = $this->visit($o->left, $collector);

        if (property_exists($right, 'expr') and is_null($right->expr))
            return $collector->add(" IS NULL");
        else {
            $collector->add(" = ");
            return $this->visit($right, $collector);
        }
    }

    public function visit_Pharel_Attribute($o, $collector) {
        if ($o->relation->table_alias)
            $join_name = $o->relation->table_alias;
        else
            $join_name = $o->relation->name;
        
        return $collector->add($this->quote_table_name($join_name) . "." . $this->quote_column_name($o->name));
    }

    public function visit_array($o, $collector) {
        return $this->inject_join($o, $collector, ", ");
    }

    public function maybe_visit($thing, $collector) {
        if (!$thing)
            return $collector;

        $collector->add(" ");
        return $this->visit($thing, $collector);
    }

    public function literal($o, $collector) {
        return $collector->add($o);
    }

    public function visit_Pharel_Nodes__And($o, $collector) {
        return $this->inject_join($o->children, $collector, " AND ");
    }

    public function visit_Pharel_Nodes_SqlLiteral($o, $collector) {
        return $this->literal($o->value, $collector);
    }

    public function visit_Pharel_Nodes_InnerJoin($o, $collector) {
        $collector->add("INNER JOIN ");
        $collector = $this->visit($o->left, $collector);

        if ($o->right) {
            $collector->add(self::SPACE);
            return $this->visit($o->right, $collector);
        }
        else
            return $collector;
    }

    public function visit_Pharel_Nodes_On($o, $collector) {
        $collector->add("ON ");
        return $this->visit($o->expr, $collector);
    }

    public function visit_Pharel_Nodes_Group($o, $collector) {
        return $this->visit($o->expr, $collector);
    }

    public function inject_join($list, $collector, $join_str) {
        $len = count($list) - 1;

        $f = function($c, $x, $i) use($len) {
            if ($i == $len)
                return $this->visit($x, $c);
            else
                return $this->visit($x, $c)->add($join_str);
        };

        foreach ($list as $i => $x) {
            $collector = $f($collector, $x, $i);
        }
        
        return $collector;
    }

    private function quote_column_name($name) {
        return "`{$name}`";//@quoted_columns[name] ||= Arel::Nodes::SqlLiteral === name ? name : @connection.quote_column_name(name)
    }

    private function quote_table_name($name) {
        return "`{$name}`";//@quoted_columns[name] ||= Arel::Nodes::SqlLiteral === name ? name : @connection.quote_column_name(name)
    }
}
