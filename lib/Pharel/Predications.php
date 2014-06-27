<?php

namespace Pharel;

trait Predications {
    public function not_eq($other) {
        return new Nodes\NotEqual($this, Nodes::build_quoted($other, $this));
    }

    public function not_eq_any($others) {
        return $this->grouping_any("not_eq", $others);
    }

    public function not_eq_all($others) {
        return $this->grouping_all("not_eq", $others);
    }

    public function eq($other) {
        return new Nodes\Equality($this, Nodes::build_quoted($other, $this));
    }

    public function eq_any($others) {
        return $this->grouping_any("eq", $others);
    }

    public function eq_all($others) {
        return $this->grouping_all("eq", array_map(function($x) {
            return Nodes::build_quoted($x, $this);
        }, $others));
    }

    public function in($other) {
        if ($other instanceof SelectManager)
            return new Nodes\In($this, $other->ast);
        else if (is_array($other)) {
            return new Nodes\In($this, array_map(function ($x) {
                return Nodes::build_quoted($x);
            }, $other));
        } else
            return new Nodes\In($this, Nodes::build_quoted($other, $this));
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
            return new Nodes\NotIn($this, array_map(function ($x) {
                return Nodes::build_quoted($x);
            }, $other));
        } else
            return new Nodes\NotIn($this, Nodes::build_quoted($other, $this));
    }

    public function not_in_any($others) {
        return $this->grouping_any("not_in", $others);
    }

    public function not_in_all($others) {
        return $this->grouping_all("not_in", $others);
    }

    public function matches($other) {
        return new Nodes\Matches($this, Nodes::build_quoted($other, $this));
    }

    public function matches_any($others) {
        return $this->grouping_any("matches", $others);
    }

    public function matches_all($others) {
        return $this->grouping_all("matches", $others);
    }

    public function does_not_match($other) {
        return new Nodes\DoesNotMatch($this, Nodes::build_quoted($other, $this));
    }

    public function does_not_match_any($others) {
        return $this->grouping_any("does_not_match", $others);
    }

    public function does_not_match_all($others) {
        return $this->grouping_all("does_not_match", $others);
    }

    public function gteq($right) {
        return new Nodes\GreaterThanOrEqual($this, Nodes::build_quoted($right, $this));
    }

    public function gteq_any($others) {
        return $this->grouping_any("gteq", $others);
    }

    public function gteq_all($others) {
        return $this->grouping_all("gteq", $others);
    }

    public function lteq($right) {
        return new Nodes\LessThanOrEqual($this, Nodes::build_quoted($right, $this));
    }

    public function lteq_any($others) {
        return $this->grouping_any("lteq", $others);
    }

    public function lteq_all($others) {
        return $this->grouping_all("lteq", $others);
    }

    public function gt($right) {
        return new Nodes\GreaterThan($this, Nodes::build_quoted($right, $this));
    }

    public function gt_any($others) {
        return $this->grouping_any("gt", $others);
    }

    public function gt_all($others) {
        return $this->grouping_all("gt", $others);
    }

    public function lt($right) {
        return new Nodes\LessThan($this, Nodes::build_quoted($right, $this));
    }

    public function lt_any($others) {
        return $this->grouping_any("lt", $others);
    }

    public function lt_all($others) {
        return $this->grouping_all("lt", $others);
    }

    private function grouping_any($method_id, $others) {
        $nodes = array_map(function($expr) use($method_id) {
            call_user_func([ $this, $method_id ], $expr);
        }, $others);

        $memo = array_shift($nodes);
        foreach ($nodes as $node) {
            $memo = new Nodes\_Or($memo, $node);
        }

        return new Nodes\Grouping($memo);
    }

    private function grouping_all($method_id, $others) {
        return new Nodes\Grouping(new Nodes\_And(array_map(function($expr) use($method_id) {
            call_user_func([ $this, $method_id ], $expr);
        }, $others)));
    }
}
