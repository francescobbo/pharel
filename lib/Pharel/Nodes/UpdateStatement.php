<?php

namespace Pharel\Nodes;

class UpdateStatement extends Node {
    public $relation;
    public $wheres;
    public $values;
    public $orders;
    public $limit;
    public $key;

    public function __construct() {
        parent::__construct();
    
        $this->relation = null;
        $this->wheres   = [];
        $this->values   = [];
        $this->orders   = [];
        $this->limit    = null;
        $this->key      = null;
    }

    public function __clone() {
        $this->wheres = clone $this->wheres;
        $this->values = clone $this->values;
    }
}

