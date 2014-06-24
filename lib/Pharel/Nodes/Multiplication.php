<?php

namespace Pharel\Nodes;

class Multiplication extends InfixOperation {
    public function __construct($left, $right) {
        parent::__construct("*", $left, $right);
    }
}

