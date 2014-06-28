<?php

namespace Pharel\FakeRecord;

class Column {
    public $name, $type;

    public function __construct($name, $type) {
        $this->name = $name;
        $this->type = $type;
    }
}
