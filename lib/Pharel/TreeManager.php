<?php

namespace Pharel;

class TreeManager {
    use FactoryMethods;

    public $ast;
    public $engine;
    public $bind_values;

    protected $ctx;

    public function __construct($engine) {
        $this->engine = $engine;
        $this->ctx = null;
        $this->bind_values = [];
    }

    public function visitor() {
        return new Visitors\ToSql("test");//$this->engine->connection->visitor;
    }

    public function to_dot() {

    }

    public function to_sql() {
        $collector = new Collectors\SQLString();
        $collector = $this->visitor()->accept($this->ast, $collector);
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
