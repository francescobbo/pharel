<?php

class TableTest extends PHPUnit_Framework_TestCase {
    public function testImplementsArrayAccess() {
        $table = new Pharel\Table("test");
        $this->assertTrue($table instanceof ArrayAccess);
    }

    public function testArrayAccess() {
        $table = new Pharel\Table("test");
        $attribute = $table["surname"];

        $this->assertEquals("surname", $attribute->name);
        $this->assertEquals($table, $attribute->relation);
    }
}
