<?php

namespace Pharel\Visitors;

class PostgreSQL extends ToSql {
    public function visit_Pharel_Nodes_Matches($o, $collector) {
        return $this->infix_value($o, $collector, " ILIKE ");
    }

    public function visit_Pharel_Nodes_DoesNotMatch($o, $collector) {
        return $this->infix_value($o, $collector, " NOT ILIKE ");
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
}
