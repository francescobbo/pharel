<?php

namespace Pharel\Nodes;

class Window extends Node {
    public $orders;
    public $framing;
    
    public function __construct() {
        $this->orders = [];
    }
    
    public function __clone() {
        $this->orders = array_map(function($x) { return clone $x }, $this->orders);
    }
    
    public function frame($expr) {
        $this->framing = $expr;
    }

    public function rows($expr = null) {
        $this->frame(new Rows($expr));
    }

    public function range($expr = null) {
        $this->frame(new Range($expr));
    }
    
    public function order() {
        $this->orders = array_merge($this->orders, array_map(function($x) {
            if (is_string($x))
                return new SqlLiteral($x);
            else
                return $x;
        }, func_get_args());
        
        return $this;
    }
}

