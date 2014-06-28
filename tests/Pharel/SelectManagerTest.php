<?php

namespace Pharel;

class SelectManagerTest extends \PHPUnit_Framework_TestCase {
    public function setUp() {
        $table = new Table("users");

        $this->m1 = new SelectManager(Table::$g_engine, $table);
        $this->m2 = new SelectManager(Table::$g_engine, $table);
        $this->m1->project(\Pharel::star());
        $this->m2->project(\Pharel::star());
        $this->m1->where($table["age"]->lt(18));
        $this->m2->where($table["age"]->gt(99));
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
        $this->markTestIncomplete('Can\'t solve this!');

        $table = new Table("users", [ "engine" => Table::$g_engine, "as" => "foo"]);
        $mgr = $table->from($table);

        $m2 = clone $mgr;
        $m2->project("foo");

        $this->assertNotEquals($mgr->to_sql(), $m2->to_sql()); 
    }

    public function testCloneUpdatesCorrectly() {
        $this->markTestIncomplete('Can\'t solve this!');

        $table = new Table("users", [ "engine" => Table::$g_engine, "as" => "foo"]);

        $mgr = $table->from($table);
        $m2 = clone $mgr;
        $m3 = clone $m2;
        $m2->project("foo");
        
        $this->assertNotEquals($mgr->to_sql(), $m2->to_sql());
        $this->assertEquals($m3->to_sql(), $mgr->to_sql());
    }

    public function testAliasIsUsed() {
        $table = new Table("users", [ "engine" => Table::$g_engine, "as" => "foo"]);

        $mgr = $table->from($table);
        $mgr->skip(10);
        $this->assertEquals("SELECT FROM \"users\" \"foo\" OFFSET 10", $mgr->to_sql());
    }

    public function testTakeAddsAnOffset() {
        $table = new Table("users");
        $mgr = $table->from($table);
        $mgr->skip(10);
        $this->assertEquals("SELECT FROM \"users\" OFFSET 10", $mgr->to_sql());
    }

    public function testTakeChains() {
        $table = new Table("users");
        $mgr = $table->from($table);
        $this->assertEquals("SELECT FROM \"users\" OFFSET 10", $mgr->skip(10)->to_sql());
    }

    public function testOffsetWriterAsTakeAlias() {
        $table = new Table("users");
        $mgr = $table->from($table);
        $mgr->offset = 10;
        $this->assertEquals("SELECT FROM \"users\" OFFSET 10", $mgr->to_sql());
    }
    
    public function testOffsetWriterCanRemoveOffsets() {
        $table = new Table("users");
        $mgr = $table->from($table);

        $mgr->offset = 10;
        $this->assertEquals("SELECT FROM \"users\" OFFSET 10", $mgr->to_sql());
        $mgr->offset = null;
        $this->assertEquals("SELECT FROM \"users\"", $mgr->to_sql());
    }
    
    public function testOffsetReaderAsTakeAlias() {
        $table = new Table("users");
        $mgr = $table->from($table);
        $mgr->offset = 10;
        $this->assertEquals(10, $mgr->offset);
    }

    public function testExistsCreatesAnExistsClause() {
        $table = new Table("users");
        $manager = new SelectManager(Table::$g_engine, $table);
        $manager->project(new Nodes\SqlLiteral('*'));
        
        $m2 = new SelectManager($manager->engine);
        $m2->project($manager->exists());
        $this->assertEquals("SELECT EXISTS (" . $manager->to_sql() . ")", $m2->to_sql());
    }

    public function testExistsCanBeAliased() {
        $table = new Table("users");
        $manager = new SelectManager(Table::$g_engine, $table);
        $manager->project(new Nodes\SqlLiteral('*'));

        $m2 = new SelectManager($manager->engine);
        $m2->project($manager->exists()->_as('foo'));
        $this->assertEquals("SELECT EXISTS (" . $manager->to_sql() . ") AS foo", $m2->to_sql());
    }

    public function testUnionUnifiesTwoStatements() {
        $node = $this->m1->union($this->m2);

        $this->assertEquals("( SELECT * FROM \"users\" WHERE \"users\".\"age\" < 18 UNION SELECT * FROM \"users\" WHERE \"users\".\"age\" > 99 )", $node->to_sql());
    }

    public function testUnionAll() {
        $node = $this->m1->union("all", $this->m2);

        $this->assertEquals("( SELECT * FROM \"users\" WHERE \"users\".\"age\" < 18 UNION ALL SELECT * FROM \"users\" WHERE \"users\".\"age\" > 99 )", $node->to_sql());
    }
}
