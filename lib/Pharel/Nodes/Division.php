<?php

namespace Pharel\Nodes;

class Division extends InfixOperation {
    public function __construct($left, $right) {
        parent::__construct("/", $left, $right);
    }
}

