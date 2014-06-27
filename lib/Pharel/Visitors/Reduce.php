<?php

namespace Pharel\Visitors;

class Reduce extends Visitor {
    public function accept($object, $collector = null) {
        return $this->visit($object, $collector);
    }

    protected function visit($object, $collector = null) {
        $visit = $this->dispatch($object);

        try {
            return $this->$visit($object, $collector);
        } catch (\Exception $e) {

        }
    }
}