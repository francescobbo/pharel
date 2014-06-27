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

    public function testCreatesJoinNodes2() {
        $join = $this->relation->create_join("foo", "bar");
        $this->assertInstanceOf('Pharel\\Nodes\\InnerJoin', $join);
        $this->assertEquals("foo", $join->left);
        $this->assertEquals("bar", $join->right);
    }

    public function testCreatesJoinNodesWithClass() {
        $join = $this->relation->create_join("foo", "bar", "Pharel\\Nodes\\FullOuterJoin");
        $this->assertInstanceOf('Pharel\\Nodes\\FullOuterJoin', $join);
        $this->assertEquals("foo", $join->left);
        $this->assertEquals("bar", $join->right);
    }

    public function testCreatesJoinNodesWithClass2() {
        $join = $this->relation->create_join("foo", "bar", "Pharel\\Nodes\\OuterJoin");
        $this->assertInstanceOf('Pharel\\Nodes\\OuterJoin', $join);
        $this->assertEquals("foo", $join->left);
        $this->assertEquals("bar", $join->right);
    }

    public function testCreatesJoinNodesWithClass3() {
        $join = $this->relation->create_join("foo", "bar", "Pharel\\Nodes\\RightOuterJoin");
        $this->assertInstanceOf('Pharel\\Nodes\\RightOuterJoin', $join);
        $this->assertEquals("foo", $join->left);
        $this->assertEquals("bar", $join->right);
    }
}
