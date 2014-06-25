<?php

namespace Pharel;

class DeleteManager < TreeManager {
    public function __construct($engine) {
        parent::__construct($engine);
        $this->ast = new Nodes\DeleteStatement;
        $this->ctx = $this->ast;
    }

    public function from($relation) {
        $this->ast->relation = $relation;
        return $this;
    }

    public function __set($var, $val) {
        if ($var == 'wheres') {
            return $this->ast->wheres = $val;
        } else {
            throw new Exception("cannot set!");
        }
    }
}
