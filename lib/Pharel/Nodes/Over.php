<?php

namespace Pharel\Nodes;

class Over extends Binary {
    use \Pharel\AliasPredication;

    public function __construct($left, $right = null) {
        parent::__construct($left, $right);
    }

    public function operator() {
        return "OVER";
    }
}

