<?php

namespace Pharel;

trait Math {
    public function mul($other) {
        return new Nodes\Multiplication($this, $other);
    }

    public function plus($other) {
        return new Nodes\Grouping(new Nodes\Addition($this, $other));
    }

    public function minus($other) {
        return new Nodes\Grouping(new Nodes\Subtraction($this, $other));
    }

    public function div($other) {
        return new Nodes\Division($this, $other);
    }
}