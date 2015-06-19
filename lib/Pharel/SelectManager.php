<?php

namespace Pharel;

class SelectManager extends TreeManager {
    use Crud;

    public $join_sources;
    public $projections;

    public function __construct($table = null) {
        parent::__construct();
        $this->ast = new Nodes\SelectStatement();
        $this->ctx = $this->ast->cores[count($this->ast->cores) - 1];
        $this->from($table);
        $this->projections = &$this->ctx->projections;
        $this->join_sources = &$this->ctx->source->right;
    }

    public function __clone() {
        parent::__clone();
        $this->ctx = clone $this->ast->cores[count($this->ast->cores) - 1];
        $this->projections = &$this->ctx->projections;
        $this->join_sources = &$this->ctx->source->right;
    }

    public function limit() {
        if ($this->ast->limit)
            return $this->ast->limit->expr;
        else
            return null;
    }

    public function taken() {
        return $this->limit();
    }

    public function constraints() {
        return $this->ctx->wheres;
    }

    public function skip($amount) {
        if ($amount)
            $this->ast->offset = new Nodes\Offset($amount);
        else
            $this->ast->offset = null;

        return $this;
    }

    public function exists() {
        return new Nodes\Exists($this->ast);
    }

    public function _as($other) {
        return $this->create_table_alias($this->grouping($this->ast), new Nodes\SqlLiteral($other));
    }

    public function lock($locking = true) {
        if ($locking === true) {
            $locking = \Pharel::sql("FOR UPDATE");
        } else if ($locking instanceof Nodes\SqlLiteral) {
            $locking = \Pharel::sql($locking->value);
        } else if (is_string($locking)) {
            $locking = \Pharel::sql($locking);
        }

        $this->ast->lock = new Nodes\Lock($locking);
        return $this;
    }

    public function locked() {
        return $this->ast->lock;
    }

    public function on() {
        $exprs = func_get_args();
        $this->ctx->source->right[count($this->ctx->source->right) - 1]->right = new Nodes\On($this->collapse($exprs));
        return $this;
    }

    public function group() {
        $columns = func_get_args();
        foreach ($columns as $column) {
            if (is_string($column))
                $column = new Nodes\SqlLiteral($column);
            $this->ctx->groups[] = new Nodes\Group($column);
        }

        return $this;
    }

    public function from($table) {
        if (is_string($table))
            $table = new Nodes\SqlLiteral($table);

        if ($table instanceof Nodes\Join)
            $this->ctx->source->right[] = $table;
        else
            $this->ctx->source->left = $table;

        return $this;
    }

    public function froms() {
        return array_filter(array_map(function($x) {
            return $x->from();
        }, $this->ast->cores));
    }

    public function join($relation, $klass = "\\Pharel\\Nodes\\InnerJoin") {
        if (!$relation)
            return $this;

        if (is_string($relation) or $relation instanceof Nodes\SqlLiteral) {
            $klass = "\\Pharel\\Nodes\\StringJoin";
        }

        $this->ctx->source->right[] = $this->create_join($relation, null, $klass);
        return $this;
    }

    public function outer_join($relation) {
        return $this->join($relation, "Nodes\\OuterJoin");
    }

    public function having($expr) {
        $this->ctx->havings[] = $expr;
        return $this;
    }

    public function window($name) {
        $window = new Nodes\NamedWindow($name);
        $this->ctx->windows[] = $window;
        return $window;
    }

    public function project() {
        $projections = func_get_args();

        $this->ctx->projections = array_merge($this->ctx->projections, array_map(function($x) {
            if (is_string($x))
                return new Nodes\SqlLiteral($x);
            else
                return $x;
        }, $projections));

        return $this;
    }

    public function distinct($value = true) {
        if ($value)
            $this->ctx->set_quantifier = new Nodes\Distinct();
        else
            $this->ctx->set_quantifier = null;

        return $this;
    }

    public function distinct_on($value) {
        if ($value)
            $this->ctx->set_quantifier = new Nodes\DistinctOn($value);
        else
            $this->ctx->set_quantifier = null;

        return $this;
    }

    public function order() {
        $expr = func_get_args();

        $this->ast->orders = array_merge($this->ast->orders, array_map(function($x) {
            if (is_string($x))
                return new Nodes\SqlLiteral($x);
            else
                return $x;
        }, $expr));

        return $this;
    }

    public function orders() {
        return $this->ast->orders;
    }

    public function where_sql($engine = Table::$g_engine) {
        if (empty($this->ctx->wheres))
            return null;

        $viz = new Visitors\WhereSql($engine->connection);
        return new Nodes\SqlLiteral($viz->accept($this->ctx, new Collectors\SQLString())->value);
    }

    public function union($operation, $other = null) {
        if ($other !== null)
            $node_class = "Pharel\\Nodes\\Union" . ucfirst($operation);
        else {
            $other = $operation;
            $node_class = "Pharel\\Nodes\\Union";
        }
      
        return new $node_class($this->ast, $other->ast);
    }

    public function intersect($other) {
        return new Nodes\Intersect($this->ast, $other->ast);
    }

    public function except($other) {
        return new Nodes\Except($this->ast, $other->ast);
    }

    public function minus($other) {
        return $this->except($other);
    }

    public function with() {
        $subqueries = func_get_args();

        if (is_string($subqueries[0]))
            $node_class = "Pharel\\Nodes\\With" . ucfirst(array_shift($subqueries));
        else
            $node_class = "Pharel\\Nodes\\With";
      
        $this->ast->with = new $node_class($subqueries);    //$subqueries->flatten()

        return $this;
    }

    public function take($limit) {
        if ($limit) {
            $this->ast->limit = new Nodes\Limit($limit);
            $this->ctx->top   = new Nodes\Top($limit);
        } else {
            $this->ast->limit = null;
            $this->ctx->top   = null;
        }

        return $this;
    }
    
    public function source() {
        return $this->ctx->source;
    }

    public function __get($var) {
        switch ($var) {
            case "offset":
                if ($this->ast->offset)
                    return $this->ast->offset->expr;
                else
                    return null;
            default:
                throw new \Exception("cannot get");
        }
    }

    public function __set($var, $val) {
        switch ($var) {
            case "offset":
                return $this->skip($val);
            case "limit":
                return $this->take($val);
            default:
                throw new \Exception("cannot set");
        }
    }

    protected function collapse($exprs, $existing = null) {
        if ($existing !== null)
            $exprs = array_unshift($exprs, $existing->expr);

        $exprs = array_map(function($expr) {
            if (is_string($expr))
                return \Pharel::sql($expr);
            else
                return $expr;
        }, array_filter($exprs));

        if (count($exprs) == 1)
            return $exprs[0];
        else
            return $this->create_and($exprs);
    }
}
