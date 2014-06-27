<?php

namespace Pharel\Collectors;

class Bind {
    public function __construct() {
        $this->parts = [];
    }

    public function add($str) {
        $this->parts[] = $str;
        return $this;
    }

    public function add_bind($bind) {
        $this->parts[] = $bind;
        return $this;
    }

    public function value() {
        return $this->parts;
    }

    public function substitute_binds($bvs) {
        $bvs = clone $bvs;

        return array_map(function($val) use ($bvs) {
            if ($val instanceof Nodes\BindParam)
                return array_shift($bvs);
            else
                return $val;
        }, $this->parts);
    }

    public function compile($bvs) {
        return implode('', $this->substitute_binds($bvs));
    }
}
