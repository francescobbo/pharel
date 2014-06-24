<?php

namespace Pharel\Nodes;

class SelectCore extends Node {
    public $top;
    public $projections;
    public $wheres;
    public $groups;
    public $windows;
    public $having;
    public $source;
    public $set_quantifies;
    
    public function __construct() {
        parent::__construct();

        $this->source = new JoinSource(null);
        $this->top = null;

        $this->set_quantifier = null;
        $this->projections    = [];
        $this->wheres         = [];
        $this->groups         = [];
        $this->having         = null;
        $this->windows        = [];
        
        $this->from = &$this->source;
        $this->froms = &$this->source;
    }
    
    public function __clone() {
        if ($this->source)
            $this->source = clone $this->source;
        if ($this->having)
            $this->having = clone $this->having;

		$this->projections = clone $this->projections;
        $this->wheres = clone $this->wheres;
        $this->groups = clone $this->groups;
        $this->windows = clone $this->windows;
    }    
}

