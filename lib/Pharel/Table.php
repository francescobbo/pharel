<?php

namespace Pharel;

class Table implements \ArrayAccess {
    use Crud;
    use FactoryMethods;

    public static $g_engine;
    public $name, $engine, $aliases, $table_alias;

    public function __construct($table_name, $engine = null) {
        if (self::$g_engine === null)
            self::$g_engine = new FakeRecord\ConnectionPool;

        if ($engine === null)
            $engine = self::$g_engine;

        $this->table_name = &$this->name;
        $this->table_name = $table_name;

        $this->engine = $engine;
        $this->columns = null;
        $this->aliases = [];
        $this->table_alias = null;
        $this->primary_key = null;

        if (is_array($engine)) {
            $this->engine = isset($engine["engine"]) ? $engine["engine"] : self::$g_engine;
            if (isset($engine["as"]) and $engine["as"] != $this->name)
                $this->table_alias = $enigne["as"];
        }
    }

    public function primary_key() {
        if (!$this->primary_key) {
            $primary_key_name = $this->engine->connection->primary_key($this->name);
            if ($primary_key_name)
                $this->primary_key = $this[$primary_key_name];
        }

        return $this->primary_key;
    }

    public function alias($name = null) {
        if (!$name)
            $name = $this->name . "_2";

        $ta = Nodes\TableAlias($this, $name);
        $this->aliases[] = $ta;

        return $ta;
    }

    public function from($table) {
        return new SelectManager($this->engine, $table);
    }

    public function join($relation, $klass = "\Pharel\Nodes\InnerJoin") {
        if (!$relation)
            return $this->from($this);

        if (is_string($relation) or $relation instanceof Nodes\SqlLiteral)
            $klass = "\Pharel\Nodes\StringJoin";

        return $this->from($this)->join($relation, $klass);
    }

    public function outer_join($relation) {
        return $this->join($relation, "Nodes\OuterJoin");
    }

    public function group() {
        return $this->from($this)->group(func_get_args());
    }

    public function order() {
        return $this->from($this)->order(func_get_args());
    }

    public function where($condition) {
        return $this->from($this)->where($condition);
    }

    public function project() {
        return $this->from($this)->project(func_get_args());
    }

    public function take($amount) {
        return $this->from($this)->take($amount);
    }

    public function skip($amount) {
        return $this->from($this)->skip($amount);
    }

    public function having($expr) {
        return $this->from($this)->having($expr);
    }

    public function select_manager() {
        return new SelectManager($this->engine);
    }

    public function insert_manager() {
        return new InsertManager($this->engine);
    }

    public function update_manager() {
        return new UpdateManager($this->engine);
    }

    public function delete_manager() {
        return new DeleteManager($this->engine);
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
        throw new Exception("Cannot set an attribute!");
    }

    public function offsetExists($offset) {
        return true;
    }

    public function offsetUnset($offset) {
        throw new Exception("Cannot unset an attribute!");
    }

    public function offsetGet($name) {
        return new Attribute($this, $name);
    }
}
