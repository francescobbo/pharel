<?php

namespace Pharel;

trait OrderPredications {
    public function asc() {
        return new Nodes\Ascending($this);
    }

    public function desc() {
        return new Nodes\Descending($this);
    }
}
