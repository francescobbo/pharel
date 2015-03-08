<?php

namespace Pharel\Nodes;

class DeleteStatement extends Binary {
    public $relation, $wheres, $limit;

    public function __construct($relation = null, $wheres = []) {
        parent::__construct($relation, $wheres);
        
        $this->relation = &$this->left;
        $this->wheres = &$this->right;
    }

    public function __clone() {
        $this->right = clone $this->right;
    }
}

