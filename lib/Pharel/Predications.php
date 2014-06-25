<?php

namespace Pharel;

trait Predications {
    public function eq($other) {
        return new Nodes\Equality($this, Nodes::build_quoted($other, $this));
    }
}
