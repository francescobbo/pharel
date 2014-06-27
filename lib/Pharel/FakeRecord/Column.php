<?php

namespace Pharel\FakeRecord;

class Column {
    public function __construct($name, $type) {
        $this->name = $name;
        $this->type = $type;
    }
}
