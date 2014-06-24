<?php

namespace Pharel\Nodes;

class Ascending extends Ordering {
    public function reverse() {
        return new Descending($this->expr);
    }
    
    public function direction() {
        return "asc";
    }
    
    public function is_ascending() {
        return true;
    }
    
    public function is_descending() {
        return false;
    }
}

