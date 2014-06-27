<?php

namespace Pharel\FakeRecord;

class Spec {
    public $config;
    public function __construct($config) {
        $this->config = $config;
    }
}

class ConnectionPool {
    public $spec;
    public $connection;

    public function __construct() {
        $this->spec = new Spec([ 'adapter' => 'italy' ]);
        $this->connection = new Connection;
        $this->connection->visitor = new \Pharel\Visitors\ToSql($this->connection);
    }

    public function with_connection(callable $block) {
        $block($this->connection);
    }

    public function column_hash() {
        return $this->connection->columns_hash;
    }

    public function table_exists($table_name) {
        return $this->connection->table_exists($table_name);
    }

    public function schema_cache() {
        return $this->connection;
    }

    public function quote($thing, $column = null) {
       return $this->connection->quote($thing, $column);
    }
}
