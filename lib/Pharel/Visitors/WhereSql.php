<?php

namespace Pharel\Visitors;

class WhereSql extends ToSql {
    public function visit_Pharel_Nodes_SelectCore($o, $collector) {
        $collector->add("WHERE ");
        return $this->inject_join($o->wheres, $collector, ' AND ');
    }
}
