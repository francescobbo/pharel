<?php

namespace Pharel\Visitors;

class Visitor {
    private static $dispatch;

    public function accept($object) {
        return $this->visit($object);
    }

    public function dispatch($object) {
        $klass = get_class($this);

        if (is_object($object))
            $obj_klass = get_class($object);
        else
            $obj_klass = gettype($object);

        if (!isset(self::$dispatch[$klass]))
            self::$dispatch[$klass] = [];

        if (!isset(self::$dispatch[$klass][$obj_klass])) {
            $obj_klass = str_replace("\\", "_", $obj_klass);
            self::$dispatch[$klass][$obj_klass] = "visit_{$obj_klass}";
        }

        return self::$dispatch[$klass][$obj_klass];
    }

    protected function visit($object) {
        $visit = $this->dispatch($object);

        try {
            return $this->$visit($object);
        } catch (Exception $e) {

        }
    }
}
