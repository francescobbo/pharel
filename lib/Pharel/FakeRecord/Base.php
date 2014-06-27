<?php

namespace Pharel\FakeRecord;

class Base {
    public $connection_pool;

    public function __construct() {
        $this->connection_pool = new ConnectionPool();
    }

    public function connection() {
        return $this->connection_pool->connection;
    }
}
