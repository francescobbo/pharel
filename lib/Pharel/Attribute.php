<?php

namespace Pharel;

class Attribute {
    use Expressions;
    use Predications;
    use AliasPredication;
    use OrderPredications;
    use Math;

    public $relation;
    public $name;

    public function __construct($relation, $name) {
        $this->relation = $relation;
        $this->name = $name;
    }

    public function type_cast_for_database($value) {
        $this->relation->type_cast_for_database($name, $value);
    }

    public function able_to_type_cast() {
        $relation->able_to_type_cast();
    }
}
