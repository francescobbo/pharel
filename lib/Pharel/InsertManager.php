<?php

namespace Pharel;

class InsertManager extends TreeManager {
    public function __construct($engine) {
        parent::__construct($engine);
        $this->ast = new Nodes\InsertStatement();
    }

    public function select($select) {
        $this->ast->select = $select;
    }

    public function into($table) {
        $this->ast->relation = $table;
        return $this;
    }

    public function columns() {
        return $this->ast->columns;
    }

    public function insert($fields) {
        if (empty($fields))
            return;

        if (is_string($fields)) {
            $this->ast->values = new Nodes\SqlLiteral($fields);
        } else {
            if (!$this->ast->relation)
                $this->ast->relation = $fields[0][0]->relation;

            $values = [];

            foreach ($fields as $column => $value) {
                $this->ast->columns[] = $column;
                $values[]  = $value;
            }

            $this->ast->values = $this->create_values($values, $this->ast->columns);
        }
    }

    public function create_values($values, $columns) {
        return new Nodes\Values($values, $columns);
    }

    public function __set($var, $val) {
        if ($var == 'values') {
            return $this->ast->values = $val;
        } else {
            throw new \Exception("cannot set");
        }
    }
}
