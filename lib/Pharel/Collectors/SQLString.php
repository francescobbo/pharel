<?php

namespace Pharel\Collectors;

class SQLString extends PlainString {
    public function __construct() {
      parent::__construct();
      $this->bind_index = 1;
    }

    public function add_bind($bind, $block) {
        $this->add($block());
        $this->bind_index += 1;
        return $this;
    }

    public function compile($bvs) {
        return $this->str;
    }
}
