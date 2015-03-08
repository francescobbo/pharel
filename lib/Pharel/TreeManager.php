<?php

namespace Pharel;

class TreeManager {
    use FactoryMethods;

    public $ast;
    public $bind_values;

    protected $ctx;

    public function __construct() {
        $this->ctx = null;
        $this->bind_values = [];
    }

    public function to_dot() {

    }

    public function to_sql($engine = Table::$g_engine) {
        $collector = new Collectors\SQLString();
        $collector = $engine->connection->visitor()->accept($this->ast, $collector);
        return $collector->str;
    }

    public function __clone() {
        $this->ast = clone $this->ast;
    }

    public function where($expr) {
        if ($expr instanceof TreeManager)
            $expr = $expr->ast;

        $this->ctx->wheres[] = $expr;
        return $this;
    }
}
