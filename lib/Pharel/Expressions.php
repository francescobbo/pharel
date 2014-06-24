<?php

namespace Pharel;

trait Expressions {
    public function count($distinct = false) {
        return new Nodes\Count([$this], $distinct);
    }

    public function sum() {
        return new Nodes\Sum([$this], new Nodes\SqlLiteral('sum_id'));
    }

    public function maximum() {
        return new Nodes\Max([$this], new Nodes\SqlLiteral('max_id'));
    }

    public function minimum() {
        return new Nodes\Min([$this], new Nodes\SqlLiteral('min_id'));
    }

    public function average() {
        return new Nodes\Avg([$this], new Nodes\SqlLiteral('avg_id'));
    }

    public function extract($field) {
        return new Nodes\Extract([$this], $field);
    }
}
