<?php

namespace Pharel\Nodes;

class Descending extends Ordering {
    public function reverse() {
        return new Ascending($this->expr);
    }
    
    public function direction() {
        return "desc";
    }
    
    public function is_ascending() {
        return false;
    }
    
    public function is_descending() {
        return true;
    }
}

