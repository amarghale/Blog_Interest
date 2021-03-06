<?php

/**
 * Generated by PHPUnit_SkeletonGenerator on 2017-04-30 at 13:33:59.
 */
require_once '../class.user.php';

class USERTest extends PHPUnit_Framework_TestCase {

    private $user;

    protected function setUp() {
        $this->user = new USER();
    }

    protected function tearDown() {
        unset($this->user);
    }

    /**
     * @covers USER::runQuery
     * @todo   Implement testRunQuery().
     */
    public function testRunQuery() {
        $stmt = $this->user->runQuery("SELECT * from blogs");
        $stmt->execute();
        $data = $stmt->fetchAll();
        $expected_result = true;
        if (count($data) > 1) {
            $result = true;
        } else {
            $result = false;
        }
        $this->assertEquals($expected_result, $result);
    }

    public function testRegister() {
        $this->user->register("Peter", "peter01", "peter@gmail.com", "1234567", "1234567");
        $stmt = $this->user->runQuery("SELECT Full_name AS name FROM users WHERE User_name = 'peter01'");
        $stmt->execute();
        $data = $stmt->fetch();
        $result = $data['name'];
        $expected_result = "Peter";
        $this->assertEquals($expected_result, $result);
    }

    public function testDoLogin() {
        $result = $this->user->doLogin("peter01", "peter@gmail.com", "1234567");
        assert($result);
    }

    public function testPost_blog() {
        $this->user->post_blog("Blogger", "blog,blog,blog", "music", "Just Testing", 1, "me.png");
        $stmt = $this->user->runQuery("SELECT Blog AS blog FROM blogs WHERE blog_title = 'Blogger'");
        $stmt->execute();
        $data = $stmt->fetch();
        $result = $data['blog'];
        $expected_result = "Just Testing";
        $this->assertEquals($expected_result, $result);
    }

//
    public function testPost_comment() {
        $this->user->post_comment(2, 1, "good");
        $stmt = $this->user->runQuery("SELECT * FROM comments");
        $stmt->execute();
        $data = $stmt->fetchAll();
        foreach ($data as $data):
            $result = $data['comment'];
        endforeach;
        $expected = "good";
        $this->assertEquals($result, $expected);
    }

    public function testAdd_like() {
        $stmt1 = $this->user->runQuery("SELECT * FROM users WHERE User_name = 'peter01'");
        $stmt1->execute();
        $data1 = $stmt1->fetchAll();
        foreach ($data1 as $data):
            $uid = $data['User_id'];
        endforeach;
        $this->user->add_like(2, 4);
        $stmt = $this->user->runQuery("SELECT * FROM likes WHERE blog_id = 2 AND user_id = :uid");
        $stmt->bindparam(":uid", $uid);
        $stmt->execute();
        $data = $stmt->fetchAll();
        foreach ($data as $data):
            $result = $data['likes'];
        endforeach;
        $expected = 1;
        $this->assertEquals($result, $expected);
    }

    public function testDelete_blog() {
        $stmt1 = $this->user->runQuery("SELECT * FROM blogs WHERE blog_title = 'Blogger'");
        $stmt1->execute();
        $data1 = $stmt1->fetchAll();
        foreach ($data1 as $data):
            $bid = $data['Blog_id'];
        endforeach;
        $this->user->delete_blog($bid);
        $stmt = $this->user->runQuery("SELECT * FROM blogs WHERE Blog_id = :bid");
        $stmt->bindparam(":bid", $bid);
        $stmt->execute();
        $data = $stmt->fetchAll();
        if (count($data) < 1) {
            $result = true;
        } else {
            $result = false;
        }
        $expected = true;
        $this->assertEquals($expected, $result);
    }

    public function testEnable_disable_comments() {
        $this->user->enable_disable_comments(0, 1, 2);
        $stmt = $this->user->runQuery("SELECT comment_on_off  AS switch FROM blogs WHERE Blog_id = 2 AND User_id = 1");
        $stmt->execute();
        $data = $stmt->fetch();
        $result = $data['switch'];
        $expected = 0;
        $this->assertEquals($result, $expected);
    }

}
