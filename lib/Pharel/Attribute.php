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
}
