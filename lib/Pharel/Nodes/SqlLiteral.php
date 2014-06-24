<?php

namespace Pharel\Nodes;

class SqlLiteral {
    use \Pharel\Expressions;
    use \Pharel\Predications;
    use \Pharel\AliasPredication;
    use \Pharel\OrderPredications;
    
    public function __construct($value) {
       $this->value = $value;
    }
}
