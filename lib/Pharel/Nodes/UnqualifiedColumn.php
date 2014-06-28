<?php

namespace Pharel\Nodes;

class UnqualifiedColumn extends Unary {
    public $name, $relation, $column, $attribute;

    public function __construct($expr) {
        parent::__construct($expr);
        
        $this->relation = &$this->expr->relation;
        $this->column = &$this->expr->column;
        $this->name = &$this->expr->name;
        $this->attribute = &$this->expr;
    }
}

