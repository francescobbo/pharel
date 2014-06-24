<?php

namespace Pharel\Nodes;

class Addition extends InfixOperation {
    public function __construct($left, $right) {
        parent::__construct("+", $left, $right);
    }
}

