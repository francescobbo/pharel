<?php

namespace Pharel\Nodes;

class NamedFunction extends _Function {
    public $name;
    
    public function __construct($name, $expr, $aliaz = null) {
        parent::__construct($expr, $aliaz);
        $this->name = $name;
    }
}

