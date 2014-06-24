<?php

namespace Pharel\Nodes;

class Unary extends Node {
    public $expr;

    public function __construct($expr) {
        parent::__construct();
        $this->expr = $expr;
    }
}