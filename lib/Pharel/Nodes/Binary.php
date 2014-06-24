<?php

namespace Pharel\Nodes;

class Binary extends Node {
    public $left;
    public $right;

    public function __construct($left, $right) {
        parent::__construct();
        $this->left = $left;
        $this->right = $right;
    }

    public function initialize_copy($other) {
        // parent::initialize_copy($other);
        if ($this->left)
            $this->left = clone $this->left;
        if ($this->right)
            $this->right = clone $this->right;
    }
}