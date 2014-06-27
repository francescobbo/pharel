<?php

namespace Pharel;

class Nodes {
    public static function build_quoted($other, $attribute = null) {
        if ($other instanceof Nodes\Node or
            $other instanceof Attribute or
            $other instanceof Table or
            $other instanceof Nodes\BindParam or
            $other instanceof SelectManager) {
            return $other;
        } else {
            if ($attribute instanceof \Pharel\Attribute)
                return new Nodes\Casted($other, $attribute);
            else
                return new Nodes\Quoted($other);
        }
    }
}
