<?php

namespace Pharel\Collectors;

class SQLString extends PlainString {
    public function add_bind($bind) {
        return $this->add($bind);
    }

    public function compile($bvs) {
        return $this->str;
    }
}
