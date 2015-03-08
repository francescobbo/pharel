<?php

namespace Pharel\Nodes;

class Quoted extends Unary {
  public function is_null() {
    return is_null($this->value);
  }
}
