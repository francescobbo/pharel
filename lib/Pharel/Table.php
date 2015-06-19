<?php

namespace Pharel;

class Table implements \ArrayAccess {
    use Crud;
    use FactoryMethods;

    public static $g_engine;
    public $name, $engine, $aliases, $table_alias;
    public $table_name;
    public $columns;
    public $type_caster;

    public function __construct($table_name, $as = null, $type_caster = null) {
        if (self::$g_engine === null)
            self::$g_engine = new FakeRecord\ConnectionPool;

        $this->table_name = &$this->name;
        $this->table_name = $table_name;

        $this->engine = self::$g_engine;
        $this->columns = null;
        $this->aliases = [];
        $this->table_alias = null;
        $this->type_caster = $type_caster;

        if ($as != $this->name)
            $this->table_alias = $as;
    }

    public function alias($name = null) {
        if (!$name)
            $name = $this->name . "_2";

        $ta = new Nodes\TableAlias($this, $name);
        $this->aliases[] = $ta;

        return $ta;
    }

    public function type_cast_for_database($attribute_name, $value) {
        return $this->type_caster->type_cast_for_database($attribute_name, $value);
    }

    public function able_to_type_cast() {
        return !is_null($type_caster);
    }

    public function from() {
        return new SelectManager($this);
    }

    public function join($relation, $klass = "\\Pharel\\Nodes\\InnerJoin") {
        if (!$relation)
            return $this->from();

        if (is_string($relation) or $relation instanceof Nodes\SqlLiteral)
            $klass = "\\Pharel\\Nodes\\StringJoin";

        return $this->from()->join($relation, $klass);
    }

    public function outer_join($relation) {
        return $this->join($relation, "Nodes\\OuterJoin");
    }

    public function group() {
        $args = func_get_args();
        $sm = $this->from();
        return call_user_func_array([$sm, 'group'], $args);
    }

    public function order() {
        $args = func_get_args();
        $sm = $this->from();
        return call_user_func_array([$sm, 'order'], $args);
    }

    public function where($condition) {
        return $this->from()->where($condition);
    }

    public function project() {
        $args = func_get_args();
        $sm = $this->from();
        return call_user_func_array([$sm, 'project'], $args);
    }

    public function take($amount) {
        return $this->from()->take($amount);
    }

    public function skip($amount) {
        return $this->from()->skip($amount);
    }

    public function having($expr) {
        return $this->from()->having($expr);
    }

    private function attributes_for($columns) {
        if (!$columns)
            return null;

        return array_map(function($column) {
            $class = \Pharel\Attributes::_for($column);
            return new $class($this, $column->name);
        }, $columns);
    }

    public function offsetSet($offset, $value) {
        throw new \Exception("Cannot set an attribute!");
    }

    public function offsetExists($offset) {
        return true;
    }

    public function offsetUnset($offset) {
        throw new \Exception("Cannot unset an attribute!");
    }

    public function offsetGet($name) {
        return new Attribute($this, $name);
    }
}
