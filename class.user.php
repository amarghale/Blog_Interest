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

    public function doLogin($uname, $umail, $upass) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE User_name=:uname OR email=:umail ");
            $stmt->execute(array(':uname' => $uname, ':umail' => $umail));
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($stmt->rowCount() == 1) {
                if (password_verify($upass, $userRow['Password1'])) {
                    $_SESSION['user_session'] = $userRow['User_id'];
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

    public function post_blog($title, $blog, $user_id, $image) {
        try {

            $stmt = $this->conn->prepare("INSERT INTO blogs(blog_title, blog, User_id, blog_image) 
		                                               VALUES(:title, :blog, :User_id, :blog_image)");
            $stmt->bindparam(":title", $title);
            $stmt->bindparam(":blog", $blog);
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