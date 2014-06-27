<?php

class TableTest extends PHPUnit_Framework_TestCase {
    public $relation;

    public function setUp() {
        $this->relation = new Pharel\Table("users");
    }

    public function testCreatesJoinNodes() {
        $join = $this->relation->create_string_join("foo");
        $this->assertInstanceOf('Pharel\\Nodes\\StringJoin', $join);
        $this->assertEquals("foo", $join->left);
    }

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
