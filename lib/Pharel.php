<?php

class Pharel {
    public static function sql($raw_sql) {
        return new Pharel\Nodes\SqlLiteral($raw_sql);
    }

    public static function star() {
        return '*';
    }
}
