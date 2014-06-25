<?php

namespace Pharel\Visitors;

class Reduce extends Visitor {
    public function accept($object, $collector) {
        return $this->visit($object, $collector);
    }

    protected function visit($object, $collector) {
        $visit = $this->dispatch($object);

        try {
            return $this->$visit($object, $collector);
        } catch (Exception $e) {

        }
    }
}