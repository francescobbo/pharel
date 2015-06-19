<?php

namespace Pharel\Collectors;

class PlainString {
    public $str = '';

    public function __construct() {
    }

    public function value() {
        return $this->str;
    }

    public function add($str) {
        if ($str instanceof \Pharel\Nodes\SqlLiteral)
            $this->str .= $str->value;
        else
            $this->str .= $str;
        return $this;
    }
}
