<?php

namespace Pharel\Nodes;

class Values extends Binary {
    public function __construct($exprs, $columns = []) {
        parent::__construct($exprs, $columns);
        
        $this->expressions = &$this->left;
        $this->columns = &$this->right;
    }
}

