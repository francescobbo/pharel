<?php

namespace Pharel\Visitors;

class MySQL extends ToSql {
    public function visit_Pharel_Nodes_Union($o, $collector, $suppress_parens = false) {
        if (!$suppress_parens)
            $collector->add("( ");

        if ($o->left instanceof Nodes\Union)
            $collector = $this->visit_Pharel_Nodes_Union($o->left, $collector, true);
        else
            $collector = $this->visit($o->left, $collector);

        $collector->add(" UNION ");

        if ($o->right instanceof Nodes\Union)
            $collector = $this->visit_Pharel_Nodes_Union($o->right, $collector, true);
        else
            $collector = $this->visit($o->right, $collector);

        if ($suppress_parens)
            return $collector;
        else
            return $collector->add(" )");
    }

    public function visit_Pharel_Nodes_Bin($o, $collector)
    {
        $collector->add("BINARY ");
        return $this->visit($o->expr, $collector);
    }

    /**
     * :'(
     * http://dev.mysql.com/doc/refman/5.0/en/select.html#id3482214
     */
    public function visit_Pharel_Nodes_SelectStatement($o, $collector) {
        if ($o->offset && !$o->limit)
            $o->limit = new Nodes\Limit(Nodes::build_quoted(18446744073709551615));

        return parent::visit_Pharel_Nodes_SelectStatement($o, $collector);
    }

    public function visit_Pharel_Nodes_SelectCore($o, $collector) {
        if (is_null($o->froms))
            $o->froms = \Pharel::sql('DUAL');

        return parent::visit_Pharel_Nodes_SelectCore($o, $collector);
    }

    public function visit_Pharel_Nodes_UpdateStatement($o, $collector) {
        $collector->add("UPDATE ");
        $collector = $this->visit($o->relation, $collector);

        if (!empty($o->values)) {
            $collector->add(" SET ");
            $collector = $this->inject_join($o->values, $collector, ', ');
        }

        if (!empty($o->wheres)) {
            $collector->add(" WHERE ");
            $collector = $this->inject_join($o->wheres, $collector, ' AND ');
        }

        if (!empty($o->orders)) {
            $collector->add(" ORDER BY ");
            $collector = $this->inject_join($o->orders, $collector, ', ');
        }

        if (!is_null($o->limit)) {
            $collector->add(" ");
            return $this->visit($o->limit, $collector);
        } else
            return $collector;
    }
}