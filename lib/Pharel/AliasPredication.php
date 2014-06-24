<?php

namespace Pharel;

trait AliasPredication {
    public function _as($other) {
        return new Nodes\_As($this, new Nodes\SqlLiteral($other));
    }
}
