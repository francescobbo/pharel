<?php

namespace Pharel\Collectors;

class PlainString {
    public $str = '';

    public function value() {
        return $this->str;
    }

    public function add($str) {
        $this->str .= $str;
        return $this;
    }
}
