<?php

namespace Pharel\FakeRecord;

class Connection {
    public $tables;
    public $visitor;

    public $columns = [];
    public $columns_hash = [];
    public $primary_keys = [];

    public function __construct($visitor = null) {
        $this->tables = [ 'users', 'photos', 'developers', 'products' ];
        $this->columns = [
            'users' => [
                new Column('id', 'integer'),
                new Column('name', 'string'),
                new Column('bool', 'boolean'),
                new Column('created_at', 'date')
            ],
            'products' => [
                new Column('id', 'integer'),
                new Column('price', 'decimal')
            ]
        ];

        foreach ($this->columns as $table => $columns) {
            foreach ($columns as $column)
                $this->columns_hash[$table][$column->name] = $column;
        }

        $this->primary_keys = [
            'users' => 'id',
            'products' => 'id'
        ];

        $this->visitor = $visitor;
    }

    public function column_hash($table_name) {
        return $this->columns_hash[$table_name];
    }

    public function primary_key($table_name) {
        return $this->primary_keys[$table_name];
    }

    public function table_exists($table_name) {
        return isset($this->tables[$table_name]);
    }

    public function columns($table_name, $message = null) {
        return $this->columns[$table_name];
    }

    public function quote_table_name($name) {
        return "\"{$name}\"";
    }

    public function quote_column_name($name) {
        return "\"{$name}\"";
    }

    public function schema_cache() {
        return $this;
    }

    public function quote($thing, $column = null) {
        if ($column !== null and $thing !== null) {
            if ($column->type == "integer")
                $thing = intval($thing);
        }

        if (is_numeric($thing))
            return $thing;
        else if (is_null($thing))
            return "NULL";
        else if ($thing === true)
            return "'t'";
        else if ($thing === false)
            return "'f'";
        else if ($thing instanceof \DateTimeInterface)
            return "'{$thing->format("Y-m-d H:i:s")}'";
        else {
            $thing = str_replace("'", "\\\\'", $thing);
            return "'{$thing}'";
        }
    }
}
