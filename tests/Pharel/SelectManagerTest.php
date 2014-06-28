<?php

namespace Pharel;

class SelectManagerTest extends \PHPUnit_Framework_TestCase {
    public function setUp() {
        new Table("foo");
    }

    public function testJoinSources() {
        $manager = new SelectManager(Table::$g_engine);
        $manager->join_sources[] = new Nodes\StringJoin(Nodes::build_quoted('foo'));
        $this->assertEquals("SELECT FROM 'foo'", $manager->to_sql());
    }

    public function testManagerStoresBindValues() {
        $manager = new SelectManager(Table::$g_engine);
        $this->assertEquals([], $manager->bind_values);
        $manager->bind_values = [1];
        $this->assertEquals([1], $manager->bind_values);
    }

    public function testCloneCreatesNewCores() {
        $table = new Table("users", [ "engine" => Table::$g_engine, "as" => "foo"]);
        $mgr = $table->from($table);
        $m2 = clone $mgr;
        $m2->project("foo");

    }
}
