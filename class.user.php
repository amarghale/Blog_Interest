<?php

require_once('config/dbconfig.php');

class USER {

    private $conn;

    public function __construct() {
        $database = new Database();
        $db = $database->dbConnection();
        $this->conn = $db;
    }

    public function runQuery($sql) {
        $stmt = $this->conn->prepare($sql);
        return $stmt;
    }

    public function register($fname, $uname, $umail, $upass, $upass1) {
        try {
            $new_password1 = password_hash($upass, PASSWORD_DEFAULT);
            $new_password2 = password_hash($upass1, PASSWORD_DEFAULT);

            $stmt = $this->conn->prepare("INSERT INTO users(Full_name, email, User_name, Password1, Password2) 
		                                               VALUES(:fname, :umail, :uname, :upass1, :upass2)");
            $stmt->bindparam(":fname", $fname);
            $stmt->bindparam(":umail", $umail);
            $stmt->bindparam(":uname", $uname);
            $stmt->bindparam(":upass1", $new_password1);
            $stmt->bindparam(":upass2", $new_password2);

            $stmt->execute();

            return $stmt;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function post_comment($blog_id, $uid, $comment) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO comments (Blog_id, user_id, comment) VALUES(:Blog_id, :user_id, :comment)");
            $stmt->bindparam(":Blog_id", $blog_id);
            $stmt->bindparam(":user_id", $uid);
            $stmt->bindparam(":comment", $comment);
            $stmt->execute();
            return $stmt;
        } catch (Exception $ex) {
            echo $e->getMessage();
        }
    }

    public function add_like($blog_id, $uid) {
        try {
            $stmt1 = $this->conn->prepare("SELECT likes FROM likes WHERE blog_id = :blog_id AND user_id = :uid");
            $stmt1->bindparam(":blog_id", $blog_id);
            $stmt1->bindparam(":uid", $uid);
            $stmt1->execute();
            $like = $stmt1->fetch();
            if ($like < 1) {
                $first_like = 1;
                $stmt = $this->conn->prepare("INSERT INTO likes(blog_id, user_id, likes) VALUES(:blog_id, :user_id, :likes)");
                $stmt->bindparam(":blog_id", $blog_id);
                $stmt->bindparam(":user_id", $uid);
                $stmt->bindparam(":likes", $first_like);
                $stmt->execute();
            } else {
                $stmt = $this->conn->prepare("Update likes SET likes = likes+1 WHERE blog_id = :blog_id AND user_id = :uid");
                $stmt->bindparam(":blog_id", $blog_id);
                $stmt->bindparam(":uid", $uid);
                $stmt->execute();
            }
            return $stmt;
        } catch (Exception $ex) {
            echo $e->getMessage();
        }
    }

    public function upload_avatar($image, $uid) {
        try {
            $stmt = $this->conn->prepare("UPDATE users SET profile_picture = :image WHERE User_id = :id");
            $stmt->bindparam(":image", $image);
            $stmt->bindparam(":id", $uid);
            $stmt->execute();

            return $stmt;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function delete_blog($blog_id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM blogs WHERE Blog_id = :id");
            $stmt->bindparam(":id", $blog_id);
            $stmt->execute();
            $stmt->closeCursor();
            return $stmt;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    
    public function enable_disable_comments($switch, $uid, $bid){
        try {
            $stmt = $this->conn->prepare("UPDATE blogs SET comment_on_off = :switch WHERE User_id = :uid AND Blog_id = :bid" );
            $stmt->bindparam(":switch", $switch);
            $stmt->bindparam(":uid", $uid);
            $stmt->bindparam(":bid", $bid);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function doLogin($uname, $umail, $upass) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE User_name=:uname OR email=:umail ");
            $stmt->execute(array(':uname' => $uname, ':umail' => $umail));
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($stmt->rowCount() == 1) {
                if (password_verify($upass, $userRow['Password1'])) {
                    $_SESSION['user_session'] = $userRow['User_id'];
                    $_SESSION['loggedin_time'] = time();
                    return true;
                } else {
                    return false;
                }
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function is_loggedin() {
        if (isset($_SESSION['user_session'])) {
            return true;
        }
    }

    function isLoginSessionExpired() {
        $login_session_duration = 1800;
        $current_time = time();
        if (isset($_SESSION['user_session']) and isset($_SESSION["loggedin_time"])) {
            if ((($current_time - $_SESSION['loggedin_time']) > $login_session_duration)) {
                return true;
            }
        }
        return false;
    }

    public function post_blog($title, $tags, $category, $blog, $user_id, $image) {
        try {

            $stmt = $this->conn->prepare("INSERT INTO blogs(blog_title, tags, category_name, blog, User_id, blog_image) 
		                                               VALUES(:title, :tags, :category_name, :blog, :User_id, :blog_image)");
            $stmt->bindparam(":title", $title);
            $stmt->bindparam(":tags", $tags);
            $stmt->bindparam(":blog", $blog);
            $stmt->bindparam(":category_name", $category);
            $stmt->bindparam(":User_id", $user_id);
            $stmt->bindparam(":blog_image", $image);
            $stmt->execute();

            return $stmt;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function redirect($url) {
        header("Location: $url");
    }

    public function doLogout() {
        session_destroy();
        unset($_SESSION['user_session']);
        return true;
    }

}

?>