<?php

namespace Pharel\Nodes;

class TableAlias extends Binary implements \ArrayAccess {
    public $name, $table_alias, $relation;

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

    public function type_cast_for_database() {
        call_user_func_array([ $this->relation, 'type_cast_for_database' ], func_get_args());
    }

    public function able_to_type_cast() {
        $relation->able_to_type_cast();
    }
}

