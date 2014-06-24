<?php

namespace Pharel\Nodes;

class StringJoin extends Join {
    public function __construct($left, $right = null) {
        parent::__construct($left, $right);
    }
}

