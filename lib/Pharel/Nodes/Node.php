<?php

namespace Pharel\Nodes;

class Node {
    use \Pharel\FactoryMethods;

    public function __construct() {
    }

    public function not() {
        return new Not($this);
    }

    public function _or($right) {
        return new Grouping(new _Or($this, $right));
    }

    public function _and($right) {
        return new _And([ $this, $right ]);
    }

    public function to_sql($engine = null) {
        if ($engine === null)
            $engine = \Pharel\Table::$g_engine;

        $collector = new \Pharel\Collectors\SQLString();
        $collector = $engine->connection->visitor->accept($this, $collector);
        return $collector->value();
    }
}
