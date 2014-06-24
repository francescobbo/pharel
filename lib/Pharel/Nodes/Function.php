<?php

namespace Pharel\Nodes;

class _Function extends Node
{
    use \Pharel\Expression;

    public $expressions;
    public $alias;
    public $distinct;

    public function initialize($expr, $aliaz = null)
    {
        $this->expressions = $expr;
        $this->alias = $aliaz && new SqlLiteral($aliaz);
        $this->distinct = false;
    }

    public function _as($aliaz)
    {
        $this->alias = new SqlLiteral($aliaz);
        return $this;
    }
}
