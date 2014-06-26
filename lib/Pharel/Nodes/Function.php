<?php

namespace Pharel\Nodes;

class _Function extends Node
{
    use \Pharel\Predications;
    use \Pharel\WindowPredications;

    public $expressions;
    public $alias;
    public $distinct;

    public function __construct($expr, $aliaz = null)
    {
        $this->expressions = $expr;
        if (!is_null($aliaz))
            $this->alias = new SqlLiteral($aliaz);
        else
            $this->alias = null;
        $this->distinct = false;
    }

    public function _as($aliaz)
    {
        $this->alias = new SqlLiteral($aliaz);
        return $this;
    }
}
