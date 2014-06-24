<?php

namespace Pharel\Nodes;

class SelectStatement extends Node {
    public $cores;
    public $limit;
    public $orders;
    public $lock;
    public $offset;
    public $with;

    public function __construct($cores == null) {
        if ($cores === null) {
            $cores = [ new SelectCore() ];

        parent::__construct();
        $this->cores = $cores;
        $this->orders = [];
        $this->limit = null;
        $this->lock = null;
        $this->offset = null;
        $this->with = null;
    }

    public function __clone() {
        $this->cores  = array_map(function($x) { return clone $x; }, $this->cores);
        $this->orders  = array_map(function($x) { return clone $x; }, $this->orders);
    }
}

