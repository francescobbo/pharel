<?php

namespace Pharel;

trait Predications {
    public function not_eq($other) {
        return new Nodes\NotEqual($this, $this->quoted_node($other));
    }

    public function not_eq_any($others) {
        return $this->grouping_any("not_eq", $others);
    }

    public function not_eq_all($others) {
        return $this->grouping_all("not_eq", $others);
    }

    public function eq($other) {
        return new Nodes\Equality($this, $this->quoted_node($other));
    }

    public function eq_any($others) {
        return $this->grouping_any("eq", $others);
    }

    public function eq_all($others) {
        return $this->grouping_all("eq", $this->quoted_array($others));
    }

    public function between($begin, $end) {
        $left = $this->quoted_node($begin);
        $left = $this->quoted_node($end);

        return new Nodes\Between($this, $left->and($right));
    }

    public function in($other) {
        if ($other instanceof SelectManager)
            return new Nodes\In($this, $other->ast);
        else if (is_array($other)) {
            return new Nodes\In($this, $this->quoted_array($other));
        } else
            return new Nodes\In($this, $this->quoted_node($other));
    }

    public function in_any($others) {
        return $this->grouping_any("in", $others);
    }

    public function in_all($others) {
        return $this->grouping_all("in", $others);
    }

    public function not_in($other) {
        if ($other instanceof SelectManager)
            return new Nodes\NotIn($this, $other->ast);
        else if (is_array($other)) {
            return new Nodes\NotIn($this, $this->quoted_array($other));
        } else
            return new Nodes\NotIn($this, $this->quoted_node($other));
    }

    public function not_in_any($others) {
        return $this->grouping_any("not_in", $others);
    }

    public function not_in_all($others) {
        return $this->grouping_all("not_in", $others);
    }

    public function matches($other, $escape = null) {
        return new Nodes\Matches($this, $this->quoted_node($other), $escape);
    }

    public function matches_any($others, $escape = null) {
        return $this->grouping_any("matches", $others, $escape);
    }

    public function matches_all($others, $escape = null) {
        return $this->grouping_all("matches", $others, $escape);
    }

    public function does_not_match($other, $escape = null) {
        return new Nodes\DoesNotMatch($this, $this->quoted_node($other), $escape);
    }

    public function does_not_match_any($others, $escape = null) {
        return $this->grouping_any("does_not_match", $others, $escape);
    }

    public function does_not_match_all($others, $escape = null) {
        return $this->grouping_all("does_not_match", $others, $escape);
    }

    public function gteq($right) {
        return new Nodes\GreaterThanOrEqual($this, $this->quoted_node($right));
    }

    public function gteq_any($others) {
        return $this->grouping_any("gteq", $others);
    }

    public function gteq_all($others) {
        return $this->grouping_all("gteq", $others);
    }

    public function lteq($right) {
        return new Nodes\LessThanOrEqual($this, $this->quoted_node($right));
    }

    public function lteq_any($others) {
        return $this->grouping_any("lteq", $others);
    }

    public function lteq_all($others) {
        return $this->grouping_all("lteq", $others);
    }

    public function gt($right) {
        return new Nodes\GreaterThan($this, $this->quoted_node($right));
    }

    public function gt_any($others) {
        return $this->grouping_any("gt", $others);
    }

    public function gt_all($others) {
        return $this->grouping_all("gt", $others);
    }

    public function lt($right) {
        return new Nodes\LessThan($this, $this->quoted_node($right));
    }

    public function lt_any($others) {
        return $this->grouping_any("lt", $others);
    }

    public function lt_all($others) {
        return $this->grouping_all("lt", $others);
    }

    private function grouping_any($method_id, $others, $extras = null) {
        $nodes = array_map(function($expr) use($method_id) {
            call_user_func([ $this, $method_id ], $expr, $extras);
        }, $others);

        $memo = array_shift($nodes);
        foreach ($nodes as $node) {
            $memo = new Nodes\_Or($memo, $node);
        }

        return new Nodes\Grouping($memo);
    }

    private function grouping_all($method_id, $others, $extras = null) {
        return new Nodes\Grouping(new Nodes\_And(array_map(function($expr) use($method_id) {
            call_user_func([ $this, $method_id ], $expr, $extras);
        }, $others)));
    }

    private function quoted_node($other) {
        return Nodes::build_quoted($other, $this);
    }

    private function quoted_array($others) {
        return array_map(function($v) {
            return $this->quoted_node($v);
        }, $others);
    }
}
