<?php

namespace Pharel;

trait Crud {
    public function compile_update($values, $pk) {
        $um = new UpdateManager();

        if ($values instanceof Nodes\SqlLiteral)
            $relation = $this->ctx->from;
        else
            $relation = $values[0][0]->relation;
        
        $um->key = $pk;
        $um->table($relation);
        $um->set($values);
        if ($this->ast->limit)
            $um->take($this->ast->limit->expr);
        call_user_func_array([$um, 'order'], $this->ast->orders);
        $um->wheres = $this->ctx->wheres;
        return $um;
    }

    public function compile_insert($values) {
        $im = $this->create_insert();
        $im->insert($values);
        return $im;
    }

    public function create_insert() {
        return new InsertManager();
    }

    public function compile_delete() {
        $dm = new DeleteManager();
        if ($this->ast->limit)
            $dm->take($this->ast->limit->expr);
        $dm->wheres = $this->ctx->wheres;
        $dm->from($this->ctx->froms);
        return $dm;
    }
}