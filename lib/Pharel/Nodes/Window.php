<?php

namespace Pharel\Nodes;

class Window extends Node {
    public $orders;
    public $framing;
    public $partitions;
    
    public function __construct() {
        $this->orders = [];
        $this->partitions = [];
        $this->framing = null;
    }
    
    public function __clone() {
        $this->orders = array_map(function($x) { return clone $x; }, $this->orders);
    }

    public function partition() {
        $expr = func_get_args();

        $this->partitions = array_merge($this->partitions, array_map(function($x) {
            if (is_string($x))
                return new SqlLiteral($x);
            else
                return $x;
        }, $expr));

        return $this;
    }

    public function frame($expr) {
        return $this->framing = $expr;
    }

    public function rows($expr = null) {
        if ($this->framing)
            return new Rows($expr);
        else
            return $this->frame(new Rows($expr));
    }

    public function range($expr = null) {
        if ($this->framing)
            return new Range($expr);
        else
            return $this->frame(new Range($expr));
    }
    
    public function order() {
        $this->orders = array_merge($this->orders, array_map(function($x) {
            if (is_string($x))
                return new SqlLiteral($x);
            else
                return $x;
        }, func_get_args()));
        
        return $this;
    }
}
