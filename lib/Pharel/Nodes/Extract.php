<?php

namespace Pharel\Nodes;

class Extract extends Unary {
    use \Pharel\AliasPredication;
    use \Pharel\Predications;

    public $field;

    public function __construct($expr, $field) {
        parent::__construct($expr);
        $this->field = $field;
    }
}
