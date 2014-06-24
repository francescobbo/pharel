<?php

namespace Pharel\Nodes;

class NamedFunction extends Function {
    public $name;
    
    public function __construct($name, $expr, $aliaz = nil) {
        parent::__construct($expr, $aliaz);
        $this->name = $name;
    }
}

