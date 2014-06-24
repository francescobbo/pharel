<?php

namespace Pharel\Nodes;

class Count extends _Function {
    public function __construct($expr, $distinct = false, $aliaz = null) {
        parent::__construct($expr, $aliaz);
        $this->distinct = $distinct;
    }
}
