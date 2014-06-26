<?php

namespace Pharel\Visitors;

class SQLite extends ToSql {
    public function visit_Pharel_Nodes_Lock($o, $collector) {
        return $collector;
    }

    public function visit_Pharel_Nodes_SelectStatement($o, $collector) {
        if ($o->offset and !$o->limit)
            $o->limit = new Nodes\Limit(-1);
        return parent::visit_Pharel_Nodes_SelectStatement($o, $collector);
    }
}