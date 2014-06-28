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

    public function testReturnsAnInsertManager() {
        $im = $this->relation->compile_insert("VALUES(NULL)");
        $this->assertInstanceOf('Pharel\\InsertManager', $im);
        $im->into($this->relation);
        $this->assertEquals("INSERT INTO \"users\" VALUES(NULL)", $im->to_sql());
    }

    public function testSkipAddsAnOffset() {
        $sm = $this->relation->skip(2);
        $query = trim(preg_replace("/\s+/", " ", $sm->to_sql()));
        $this->assertEquals("SELECT FROM \"users\" OFFSET 2", $query);
    }

    public function testSelectManager() {
        $sm = $this->relation->select_manager();
        $this->assertEquals("SELECT", $sm->to_sql());
    }

    public function testUpdateManager() {
        $um = $this->relation->update_manager();
        $this->assertInstanceOf("Pharel\\UpdateManager", $um);
        $this->assertEquals($um->engine, $this->relation->engine);
    }

    public function testDeleteManager() {
        $dm = $this->relation->delete_manager();
        $this->assertInstanceOf("Pharel\\DeleteManager", $dm);
        $this->assertEquals($dm->engine, $this->relation->engine);
    }

    public function testHavingAddsHavincClause() {
        $mgr = $this->relation->having($this->relation["id"]->eq(10));
        $query = trim(preg_replace("/\s+/", " ", $mgr->to_sql()));
        $this->assertEquals("SELECT FROM \"users\" HAVING \"users\".\"id\" = 10", $query);
    }

    public function testGroupAddsGroupByClause() {
        $mgr = $this->relation->group($this->relation["id"]);
        $query = trim(preg_replace("/\s+/", " ", $mgr->to_sql()));
        $this->assertEquals("SELECT FROM \"users\" GROUP BY \"users\".\"id\"", $query);
    }

    public function testAliasCreatesProxyNode() {
        $this->assertEmpty($this->relation->aliases);

        $node = $this->relation->alias();
        $this->assertEquals([$node], $this->relation->aliases);
        $this->assertEquals("users_2", $node->name);
        $this->assertEquals($node, $node["id"]->relation);
    }

    public function testOrderAddsOrderByClause() {
        $mgr = $this->relation->order("foo");
        $query = trim(preg_replace("/\s+/", " ", $mgr->to_sql()));
        $this->assertEquals("SELECT FROM \"users\" ORDER BY foo", $query);
    }

    public function testTakeAddsLimitClause() {
        $mgr = $this->relation->take(1);
        $mgr->project(new Pharel\Nodes\SqlLiteral("*"));
        $query = trim(preg_replace("/\s+/", " ", $mgr->to_sql()));
        $this->assertEquals("SELECT * FROM \"users\" LIMIT 1", $query);
    }

    public function testProjectCanProject() {
        $mgr = $this->relation->project(new Pharel\Nodes\SqlLiteral("*"));
        $query = trim(preg_replace("/\s+/", " ", $mgr->to_sql()));
        $this->assertEquals("SELECT * FROM \"users\"", $query);
    }

    public function testProjectCanProjectMultiple() {
        $mgr = $this->relation->project(new Pharel\Nodes\SqlLiteral("*"), new Pharel\Nodes\SqlLiteral("*"));
        $query = trim(preg_replace("/\s+/", " ", $mgr->to_sql()));
        $this->assertEquals("SELECT *, * FROM \"users\"", $query);
    }

    public function testWhereReturnsTreeManager() {
        $mgr = $this->relation->where($this->relation["id"]->eq(1));
        $mgr->project($this->relation["id"]);
        $this->assertInstanceOf("Pharel\\TreeManager", $mgr);
        $query = trim(preg_replace("/\s+/", " ", $mgr->to_sql()));
        $this->assertEquals("SELECT \"users\".\"id\" FROM \"users\" WHERE \"users\".\"id\" = 1", $query);
    }

    public function testItHasName() {
        $this->assertEquals("users", $this->relation->name);
    }

    public function testItHasTableNameAlias() {
        $this->assertEquals("users", $this->relation->table_name);
    }

    public function testItHasEngine() {
        $this->assertEquals(Pharel\Table::$g_engine, $this->relation->engine);
    }

    public function testArrayAccessReturnsAnAttributeForTheRelation() {
        $column = $this->relation["id"];
        $this->assertEquals("id", $column->name);
    }

    public function testEquality() {
        $relation1 = new Pharel\Table("users", "vroom");
        $relation1->aliases = [ 'a', 'b', 'c' ];
        $relation1->table_alias = 'zomg';

        $relation2 = new Pharel\Table("users", "vroom");
        $relation2->aliases = [ 'a', 'b', 'c' ];
        $relation2->table_alias = 'zomg';

        $array = [ $relation1, $relation2 ];
        $this->assertCount(1, array_unique($array, SORT_REGULAR));
    }
}
