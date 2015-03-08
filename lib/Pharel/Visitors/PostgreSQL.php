<?php

namespace Pharel\Visitors;

class PostgreSQL extends ToSql {
    public function visit_Pharel_Nodes_Matches($o, $collector) {
        $collector = $this->infix_value($o, $collector, " ILIKE ");

        if ($o->escape)
          $collector->add(' ESCAPE ');
          return $this->visit($o->escape, $collector)
        else
          return $collector
    }

    public function visit_Pharel_Nodes_DoesNotMatch($o, $collector) {
        $collector = $this->infix_value($o, $collector, " NOT ILIKE ");

        if ($o->escape)
          $collector->add(' ESCAPE ');
          return $this->visit($o->escape, $collector)
        else
          return $collector
    }

    public function visit_Pharel_Nodes_Regexp($o, $collector) {
        return $this->infix_value($o, $collector, " ~ ");
    }

    public function visit_Pharel_Nodes_NotRegexp($o, $collector) {
        return $this->infix_value($o, $collector, " !~ ");
    }

    public function visit_Pharel_Nodes_DistinctOn($o, $collector) {
        $collector->add("DISTINCT ON ( ");
        return $this->visit($o->expr, $collector)->add(" )");
    }

    public function visit_Pharel_Nodes_BindParam($o, $collector) {
        return $collector->add_bind($o, function($i) {
            return "$" . $i;
        });
    }
}
