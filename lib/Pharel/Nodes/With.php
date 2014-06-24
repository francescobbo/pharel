<?php

namespace Pharel\Nodes;

class With extends Unary {
    public function __construct($expr) {
        $this->children = &$this->expr;
    }
}

