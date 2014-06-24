<?php

namespace Pharel\Nodes;

class InfixOperation extends Binary {
    use \Pharel\Expressions
    use \Pharel\Predications
    use \Pharel\OrderPredications
    use \Pharel\AliasPredication
    use \Pharel\Math

    public $operator;

    public function __construct($operator, $left, $right) {
        parent::__construct($left, $right);
        $this->operator = $operator;
    }
}

