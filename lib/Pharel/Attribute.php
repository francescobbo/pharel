<?php

namespace Pharel;

class Attribute {
    public $relation;
    public $name;

    public function __construct($relation, $name) {
        $this->relation = $relation;
        $this->name = $name;
    }
}
