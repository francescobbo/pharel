<?php

namespace Pharel\Nodes;

class Extract extends Unary {
    use \Pharel\Predications;

    public $field;
    public $alias;

    public function __construct($expr, $field, $aliaz = null) {
        parent::__construct($expr);
        $this->field = $field;
        $this->alias = $aliaz && new SqlLiteral($aliaz);
    }

    public function _as($aliaz) {
        $this->alias = new SqlLiteral($aliaz);
        return $this;
    }
}
