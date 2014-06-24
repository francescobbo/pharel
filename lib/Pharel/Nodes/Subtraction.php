<?php

namespace Pharel\Nodes;

class Subtraction extends InfixOperation {
    public function __construct($left, $right) {
        parent::__construct("-", $left, $right);
    }
}

