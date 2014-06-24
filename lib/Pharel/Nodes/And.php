<?php

namespace Pharel\Nodes;

class _And extends Node {
    public $children;

    public function __construct($children) {
        parent::__construct();
        $this->children = $children;
    }
    
    public function left() {
        return $this->children[0];
    }

    public function right() {
        return $this->children[1];
    }
}

