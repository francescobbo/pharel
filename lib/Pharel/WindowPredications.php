<?php

namespace Pharel;

trait WindowPredications {
    public function over($expr = null) {
      return new Nodes\Over($this, $expr);
    }
}

