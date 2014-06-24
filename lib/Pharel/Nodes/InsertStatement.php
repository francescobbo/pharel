<?php

namespace Pharel\Nodes;

class InsertStatement extends Node {
    public $relation;
    public $columns;
    public $values;
    public $select;

    public function __construct() {
        parent::__construct();
        $this->relation = null;
		$this->columns = [];
		$this->values = null;
		$this->select = null;
    }
    
    public function __clone() {
        $this->columns = clone $this->columns;
        if ($this->values)
	        $this->values = clone $this->values;
	    if ($this->select)
            $this->select = clone $this->select;
    }    
}

