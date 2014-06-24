<?php

namespace Pharel;

trait OrderPredications {
    public function asc() {
        return new Nodes\Ordering($this, 'asc');
    }

    public function desc() {
        return new Nodes\Ordering($this, 'desc');
    }
}
