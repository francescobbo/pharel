<?php

namespace Pharel\Visitors;

class ToSql extends Reduce
{
    const WHERE = ' WHERE ';
    const SPACE = ' ';
    const COMMA = ', ';
    const GROUP_BY = ' GROUP BY ';
    const ORDER_BY = ' ORDER BY ';
    const WINDOW = ' WINDOW ';
    const _AND = ' AND ';
    const DISTINCT = 'DISTINCT';

    public function __construct($connection)
    {
        $this->connection = $connection;
//        $this->schema_cache   = $connection->schema_cache;
        $this->quoted_tables = [];
        $this->quoted_columns = [];
    }

    public function visit_Pharel_Nodes_DeleteStatement($o, $collector)
    {
        $collector->add("DELETE FROM ");
        $collector = $this->visit($o->relation, $collector);
        if (count($o->wheres) > 0) {
            $collector->add(" WHERE ");
            return $this->inject_join($o->wheres, $collector, self::_AND);
        } else
            return $collector;
    }

    public function visit_Pharel_Nodes_UpdateStatement($o, $collector)
    {
        if (empty($o->orders) and $o->limit == null) {
            $wheres = $o->wheres;
        } else {
            $wheres = [new Nodes\In($o->key, [build_subselect($o->key, $o)])];
        }

        $collector->add("UPDATE ");
        $collector = $this->visit($o->relation, $collector);
        if (count($o->values) > 0) {
            $collector->add(" SET ");
            $collector = $this->inject_join($o->values, $collector, self::COMMA);
        }

        if (count($wheres) > 0) {
            $collector->add(" WHERE ");
            $collector = $this->inject_join($wheres, $collector, self::_AND);
        }

        return $collector;
    }

    public function visit_Pharel_Nodes_InsertStatement($o, $collector)
    {
        $collector->add("INSERT INTO ");
        $collector = $this->visit($o->relation, $collector);
        if (count($o->columns) > 0) {
            $cols = array_map(function ($x) {
                return $this->quote_column_name($x->name);
            }, $o->columns);

            $cols = implode(', ', $cols);
            $collector->add(" ({$cols})");
        }

        if ($o->values) {
            return $this->maybe_visit($o->values, $collector);
        } else if ($o->select) {
            return $this->maybe_visit($o->select, $collector);
        } else {
            return $collector;
        }
    }

    public function visit_Pharel_Nodes_Exists($o, $collector)
    {
        $collector->add("EXISTS (");
        $collector = $this->visit($o->expressions, $collector)->add(")");
        if ($o->alias) {
            $collector->add(" AS ");
            return $this->visit($o->alias, $collector);
        } else {
            return $collector;
        }
    }

    public function visit_Pharel_Nodes_Casted($o, $collector)
    {
        return $collector->add($this->quoted($o->val, $o->attribute));
    }

    public function visit_Pharel_Nodes_Quoted($o, $collector)
    {
        return $collector->add($this->quoted($o->expr, null));
    }

    public function visit_Pharel_Nodes_True($o, $collector)
    {
        return $collector->add("TRUE");
    }

    public function visit_Pharel_Nodes_False($o, $collector)
    {
        return $collector->add("FALSE");
    }

    public function table_exists($name)
    {
        return false; //@schema_cache.table_exists? name
    }

    public function column_for($attr)
    {
        if (!$attr)
            return null;

        $name = $attr->name;
        $table = $attr->relation->table_name;

        if (!$this->table_exists($name))
            return null;

        return $this->column_cache($table)[$name];
    }

    public function column_cache($table)
    {
        return null; //@schema_cache.columns_hash(table)
    }

    public function visit_Arel_Nodes_Values($o, $collector)
    {
        $collector->add("VALUES (");

        $len = count($o->expressions) - 1;

        foreach ($o->expressions as $i => $value) {
            if ($value instanceof Nodes\SqlLiteral)
                $collector = $this->visit($value, $collector);
            else
                $collector->add($this->quote($value, $o->columns[$i] && $this->column_for($o->columns[$i])));

            if ($i != $len)
                $collector->add(", ");
        }

        return $collector->add(")");
    }

    public function visit_Pharel_Nodes_SelectStatement($o, $collector)
    {
        if ($o->with) {
            $collector = $this->visit($o->with, $collector);
            $collector->add(self::SPACE);
        }

        $f = function ($c, $x) {
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

    public function visit_Arel_Nodes_SelectCore($o, $collector)
    {
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

    public function visit_Pharel_Nodes_Bin($o, $collector)
    {
        return $this->visit($o->expr, $collector);
    }

    public function visit_Pharel_Nodes_Distinct($o, $collector)
    {
        return $collector->add(self::DISTINCT);
    }

    public function visit_Pharel_Nodes_DistinctOn($o, $collector)
    {
        throw new \Exception("DISTINCT ON not implemented in this db.");
    }

    public function visit_Pharel_Nodes_With($o, $collector)
    {
        $collector->add("WITH ");
        return $this->inject_join($o->children, $collector, ", ");
    }

    public function visit_Pharel_Nodes_WithRecursive($o, $collector)
    {
        $collector->add("WITH RECURSIVE ");
        return $this->inject_join($o->children, $collector, ", ");
    }

    public function visit_Pharel_Nodes_Union($o, $collector)
    {
        $collector->add("( ");
        return $this->infix_value($o, $collector, " UNION ")->add(" )");
    }

    public function visit_Pharel_Nodes_UnionAll($o, $collector)
    {
        $collector->add("( ");
        return $this->infix_value($o, $collector, " UNION ALL ")->add(" )");
    }

    public function visit_Pharel_Nodes_Except($o, $collector)
    {
        $collector->add("( ");
        return $this->infix_value($o, $collector, " EXCEPT ")->add(" )");
    }

    public function visit_Pharel_Nodes_NamedWindow($o, $collector)
    {
        $collector->add($this->quote_column_name($o->name));
        $collector->add(" AS ");
        return $this->visit_Pharel_Nodes_Window($o, $collector);
    }

    public function visit_Pharel_Nodes_Window($o, $collector)
    {
        $collector->add("(");

        if (count($o->partitions)) {
            $collector->add("PARTITION BY ");
            $collector = $this->inject_join($o->partitions, $collector, ", ");
        }

        if (count($o->orders)) {
            if (count($o->partitions))
                $collector->add(' ');
            $collector->add("ORDER BY ");
            $collector = $this->inject_join($o->orders, $collector, ", ");
        }

        if ($o->framing) {
            if (count($o->partitions) or count($o->orders))
                $collector->add(' ');

            $collector = $this->visit($o->framing, $collector);
        }

        return $collector->add(")");
    }

    public function visit_Pharel_Nodes_Rows($o, $collector)
    {
        if ($o->expr) {
            $collector->add("ROWS ");
            return $this->visit($o->expr, $collector);
        } else
            return $collector->add("ROWS");
    }

    public function visit_Pharel_Nodes_Range($o, $collector)
    {
        if ($o->expr) {
            $collector->add("RANGE ");
            return $this->visit($o->expr, $collector);
        } else
            return $collector->add("RANGE");
    }

    public function visit_Pharel_Nodes_Preceding($o, $collector)
    {
        if ($o->expr)
            $collector = $this->visit($o->expr, $collector);
        else
            $collector->add("UNBOUNDED");

        return $collector->add(" PRECEDING");
    }

    public function visit_Pharel_Nodes_Following($o, $collector)
    {
        if ($o->expr)
            $collector = $this->visit($o->expr, $collector);
        else
            $collector->add("UNBOUNDED");

        return $collector->add(" FOLLOWING");
    }

    public function visit_Pharel_Nodes_CurrentRow($o, $collector) {
        return $collector->add("CURRENT ROW");
    }

    public function visit_Pharel_Nodes_Over($o, $collector) {
        if (is_null($o->right))
            return $this->visit($o->left, $collector)->add(" OVER ()");
        else if ($o->right instanceof Nodes\SqlLiteral)
            return $this->infix_value($o, $collector, " OVER ");
        else if (is_string($o->right))
            return $this->visit($o->left, $collector)->add(" OVER " . $this->quote_column_name($o->right));
        else
            return $this->infix_value($o, $collector, " OVER ");
    }

    public function visit_Pharel_Nodes_Having($o, $collector) {
        $collector->add("HAVING ");
        return $this->visit($o->expr, $collector);
    }

    public function visit_Pharel_Nodes_Offset($o, $collector) {
        $collector->add("OFFSET ");
        return $this->visit($o->expr, $collector);
    }

    public function visit_Pharel_Nodes_Limit($o, $collector) {
        $collector->add("LIMIT ");
        return $this->visit($o->expr, $collector);
    }

    public function visit_Pharel_Nodes_Top($o, $collector) {
        return $collector;
    }

    public function visit_Pharel_Nodes_Lock($o, $collector) {
        return $this->visit($o->expr, $collector);
    }

    public function visit_Pharel_Nodes_Grouping($o, $collector) {
        $collector->add("(");
        return $this->visit($o->expr, $collector)->add(")");
    }

    public function visit_Pharel_SelectManager($o, $collector) {
        return $collector->add("(" . rtrim($o->to_sql()) . ")");
    }

    public function visit_Pharel_Nodes_Ascending($o, $collector) {
        return $this->visit($o->expr, $collector)->add(" ASC");
    }

    public function visit_Pharel_Nodes_Descending($o, $collector) {
        return $this->visit($o->expr, $collector)->add(" DESC");
    }

    public function visit_Pharel_Nodes_Group($o, $collector) {
        return $this->visit($o->expr, $collector);
    }

    public function visit_Pharel_Nodes_NamedFunction($o, $collector) {
        $collector->add($o->name);
        $collector->add("(");
        if ($o->distinct)
            $collector->add("DISTINCT ");

        $collector = $this->inject_join($o->expressions, $collector, ", ")->add(")");
        if ($o->alias) {
            $collector->add(" AS ");
            return $this->visit($o->alias, $collector);
        } else
            return $collector;
    }

    public function visit_Pharel_Nodes_Extract($o, $collector) {
        $collector->add("EXTRACT(" . strtoupper($o->field) . " FROM ");
        $collector = $this->visit($o->expr, $collector)->add(")");

        if ($o->alias) {
            $collector->add(" AS ");
            return $this->visit($o->alias, $collector);
        } else
            return $collector;
    }

    public function visit_Pharel_Nodes_Count($o, $collector) {
        return $this->aggregate("COUNT", $o, $collector);
    }

    public function visit_Pharel_Nodes_Sum($o, $collector) {
        return $this->aggregate("SUM", $o, $collector);
    }

    public function visit_Pharel_Nodes_Max($o, $collector) {
        return $this->aggregate("MAX", $o, $collector);
    }

    public function visit_Pharel_Nodes_Min($o, $collector) {
        return $this->aggregate("MIN", $o, $collector);
    }

    public function visit_Pharel_Nodes_Avg($o, $collector) {
        return $this->aggregate("AVG", $o, $collector);
    }

    public function quoted($o, $a) {
        return $this->quote($o, $this->column_for($a));
    }

    public function quote($value, $column = null) {
        if ($value instanceof Nodes\SqlLiteral)
            return $value;

        return "\"" . addslashes($value) . "\""; //@connection.quote value, column
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

    public function inject_join($list, $collector, $join_str) {
        $len = count($list) - 1;

        $f = function($c, $x, $i) use($len, $join_str) {
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

    public function infix_value($o, $collector, $value) {
        $collector = $this->visit($o->left, $collector);
        $collector->add($value);
        return $this->visit($o->right, $collector);
    }

    public function aggregate($name, $o, $collector) {
        $collector->add($name . "(");
        if ($o->distinct)
            $collector->add("DISTINCT ");

        $collector = $this->inject_join($o->expressions, $collector, ", ")->add(")");
        if ($o->alias) {
            $collector->add(" AS ");
            return $this->visit($o->alias, $collector);
        } else
            return $collector;
    }

    private function quote_column_name($name) {
        return "`{$name}`";//@quoted_columns[name] ||= Arel::Nodes::SqlLiteral === name ? name : @connection.quote_column_name(name)
    }

    private function quote_table_name($name) {
        return "`{$name}`";//@quoted_columns[name] ||= Arel::Nodes::SqlLiteral === name ? name : @connection.quote_column_name(name)
    }
}
