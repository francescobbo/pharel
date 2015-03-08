<?php

namespace Pharel\Nodes;

class Matches extends Binary {
  public $escape;

  public function __construct($left, $right, $escape = null) {
    parent::__construct($left, $right);
    if ($escape) {
      $this->escape = Nodes::build_quoted($escape)
    }
  }
}
