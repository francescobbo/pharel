<?php

namespace Pharel\Nodes;

class Equality extends Binary {
    public function __construct($operator1, $operator2) {
        parent::__construct($operator1, $operator2);
        $this->operand1 = &$this->left;
        $this->operand2 = &$this->right;
    }

    public function operator() {
        return "==";
    }
}

