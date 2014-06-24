<?php

namespace Pharel {
    class Table implements \ArrayAccess {
        public function __construct($table_name) {
            $this->table_name = $table_name;
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
}