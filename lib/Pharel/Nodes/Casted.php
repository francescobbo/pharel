<?php

namespace Pharel\Nodes;

class Casted extends Node {
    public $val;
    public $attribute;

    public function __construct($val, $attribute) {
        $this->val = $val;
        $this->attribute = $attribute;

        parent::__construct();
    }

    public function is_null() {
        return is_null($this->val);
    }
}
