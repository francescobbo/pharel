<?php

namespace Pharel\Nodes;

class TableAlias extends Binary implements ArrayAccess {
    public function __construct($name, $relation) {
        parent::__construct($name, $relation);
        
        $this->name = &$this->right;
        $this->table_alias = &$this->right;
        $this->relation = &$this->left;
    }

    public function table_name() {
        if (isset($this->relation->name))
            return $this->relation->name;
        else
            return $this->name;
    }
    
    public function engine() {
        return $this->engine;
    }

    public function offsetGet($name) {
        return new \Pharel\Attribute($this, $name);
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
}

