<?php

namespace Pharel;

trait FactoryMethods {
    public function create_true() {
        return new Nodes\True;
    }
    
    public function create_false() {
        return new Nodes\False;
    }
    
    public function create_table_alias($relation, $name) {
        return new Nodes\TableAlias($relation, $name);
    }

    public function create_join($to, $constraint = null, $klass = "\\Pharel\\Nodes\\InnerJoin") {
        return new $klass($to, $constraint);
    }

    public function create_string_join($to) {
        return create_join($to, null, "\\Pharel\\Nodes\\StringJoin");
    }

    public function create_and($clauses) {
        return new Nodes\_And($clauses);
    }

    public function create_on($expr) {
        return new Nodes\On($expr);
    }

    public function grouping($expr) {
        return new Nodes\Grouping($expr);
    }

    public function lower($column) {
        return new Nodes\NamedFunction('LOWER', [Nodes::build_quoted($column)]);
    }
}

