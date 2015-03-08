<?php

namespace Pharel;

class DeleteManager extends TreeManager {
    public function __construct() {
        parent::__construct();
        $this->ast = new Nodes\DeleteStatement;
        $this->ctx = $this->ast;
    }

    public function from($relation) {
        $this->ast->relation = $relation;
        return $this;
    }

    public function take($limit) {
        if ($limit)
            $this->ast->limit = new Nodes\Limit(Nodes::build_quoted($limit));
    }

    public function __set($var, $val) {
        if ($var == 'wheres') {
            return $this->ast->wheres = $val;
        } else {
            throw new \Exception("cannot set!");
        }
    }
}
