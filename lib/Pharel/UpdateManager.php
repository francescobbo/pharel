<?php

namespace Pharel;

/**
 * @property mixed $key alias for $ast->key
 * @property mixed $wheres alias for $ast->wheres
 */
class UpdateManager extends TreeManager {
    public function __construct() {
        parent::__construct();
        $this->ast = new Nodes\UpdateStatement();
        $this->ctx = $this->ast;
    }

    public function take($limit) {
        if ($limit)
            $this->ast->limit = new Nodes\Limit(Nodes::build_quoted($limit));
        return $this;
    }

    public function order() {
        $this->ast->orders = func_get_args();
        return $this;
    }

    public function table($table) {
        $this->ast->relation = $table;
        return $this;
    }

    public function where($expr) {
        $this->ast->wheres[] = $expr;
        return $this;
    }

    public function set($values) {
        if (is_string($values))
            $this->ast->values = [ $values ];
        else {
            $this->ast->values = array_map(function($value, $column) {
                return new Nodes\Assignment(
                    new Nodes\UnqualifiedColumn($column), $value
                );
            }, $values, array_keys($values));
        }

        return $this;
    }

    public function __get($var) {
        if ($var == "key")
            return $this->ast->key;
        else
            throw new \Exception("cannot get");
    }

    public function __set($var, $val) {
        if ($var == "wheres")
            return $this->ast->wheres = $val;
        else if ($var == "key")
            return $this->ast->key = Nodes::build_quoted($val);
        else
            throw new \Exception("cannot set");
    }
}
