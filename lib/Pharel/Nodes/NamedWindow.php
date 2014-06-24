<?php

namespace Pharel\Nodes;

class NamedWindow extends Window {
    public $name;
    
    public function __construct($name) {
        parent::__construct();
        $this->name = $name;
    }

    public function __clone() {
        parent::__clone();
        $this->name = clone $this->name;
    }
}

