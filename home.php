<?php
require_once("session.php");

require_once("class.user.php");

require_once ("filter_comments.php");
require_once ("constants.php");

$auth_user = new USER();

$user_id = $_SESSION['user_session'];

if ($auth_user->isLoginSessionExpired()) {
    session_destroy();
    $auth_user->redirect('login.php');
    echo 'Session expired';
}
$stmt = $auth_user->runQuery("SELECT * FROM users WHERE User_id=:user_id");
$stmt->execute(array(":user_id" => $user_id));

$userRow = $stmt->fetch(PDO::FETCH_ASSOC);

if (!isset($category)) {
    $category = filter_input(INPUT_GET, "category");
    if ($category == NULL || $category == " ") {
        $category = "sports";
    }
}
$stmt1 = $auth_user->runQuery("SELECT * FROM blogs, users WHERE blogs.category_name = :category AND blogs.User_id = users.User_id");
$stmt1->bindparam(":category", $category);
$stmt1->execute();
$blogs = $stmt1->fetchAll();
$stmt1->closeCursor();

$stmt2 = $auth_user->runQuery("SELECT DISTINCT category_name FROM blogs");
$stmt2->execute();
$category_names = $stmt2->fetchAll();
$stmt2->closeCursor();

if (isset($_POST['btn-comment'])) {
    $uid = $user_id;
    $blogId = strip_tags($_POST['blogId']);
    $comment = strip_tags($_POST['comment']);
    $filtered = new Sanitize($comment);
    try {
        if ($auth_user->post_comment($blogId, $uid, $filtered)) {
            $auth_user->redirect("home.php");
        }
    } catch (Exception $ex) {
        echo $e->getMessage();
    }
}

if (isset($_POST['btn-like'])) {
    $blog_Id = strip_tags($_POST['blog_id']);
    $uId = strip_tags($_POST['user_id']);
    try {
        $stmt5 = $auth_user->runQuery("SELECT * FROM likes WHERE blog_id = :bid AND user_id =:uid");
        $stmt5->bindparam(":bid", $blog_Id);
        $stmt5->bindparam(":uid", $user_id);
        $stmt5->execute();
        $data = $stmt5->fetch();
        $array_size = constants::getDataSize();
        if (count($data) > $array_size) {
            $message = "Already Liked!";
            echo "<script type='text/javascript'>alert('$message');</script>";
        } else {
            if ($auth_user->add_like($blog_Id, $uId)) {
                $auth_user->redirect("home.php");
            }
        }
    } catch (Exception $ex) {
        echo $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Home</title>
        <!-- Bootstrap Core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/vote.css" rel="stylesheet">


        <!-- Custom CSS -->
        <link href="css/blog-home.css" rel="stylesheet">
        <script src="voting.js" type="text/javascript"></script>
    </head>

    <body>

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="home.php">Blog</a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li></li>
                        <li></li>
                        <li></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <span class="glyphicon glyphicon-user"></span>&nbsp;Hi' <?php echo $userRow['email']; ?>&nbsp;<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="profile.php"><span class="glyphicon glyphicon-user"></span>&nbsp;View Profile</a></li>
                                <li><a href="logout.php?logout=true"><span class="glyphicon glyphicon-log-out"></span>&nbsp;Sign Out</a></li>
                            </ul>
                        </li>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </nav>


        <!-- Page Content -->
        <div class="container">

            <div class="row">

                <!-- Blog Entries Column -->
                <div class="col-md-8" >

                    <h1> 
                        <a href="profile.php"><span class="glyphicon glyphicon-user"></span> profile</a>
                        <a href="new_blog.php"><span class="glyphicon glyphicon-upload"></span> Post Blog</a></h1>


                    <?php ?>

                    <?php
                    foreach ($blogs as $blog):
                        $uid = $blog["User_id"];
                        ?>

                        <h2>
                            <?php echo $blog["blog_title"] ?>
                        </h2>
                        <?php
                        $stmt = $auth_user->runQuery("SELECT User_name, User_id, profile_picture FROM users WHERE User_id = $uid");
                        $stmt->execute();
                        $users = $stmt->fetchAll();
                        $stmt->closeCursor();
                        ?>
                        <?php foreach ($users as $user): ?>
                            <p class="lead">
                                by <?php
                                echo "<a href='profile.php?id=" . $user['User_id'] . "'>" . $user["User_name"] . "<br> <br>" . "</a>";
                                echo "<img src ='profile_pictures/" . $user["profile_picture"] . "' height=50 width = 50 >";
                            endforeach;
                            ?>
                            </a>
                        </p>                          
                        <p style="color: grey"><span class="glyphicon glyphicon-time"></span> Posted on <?php echo $blog["blog_date"] ?></p>
                        <hr>
                        <?php echo "<img src ='uploads/" . $blog["blog_image"] . "' height=300 width = 755 >" ?>
                        <hr>
                        <p style="text-align: justify"><strong>Tags: </strong> <?php echo $blog["tags"] ?></p>
                        <hr>
                        <p style="text-align: justify"><?php echo $blog["Blog"] ?></p>
                        <hr>
                        <?php
                        $bid = $blog['Blog_id'];
                        $stmt3 = $auth_user->runQuery("SELECT * FROM comments, users WHERE comments.Blog_id = :bid AND users.User_id = comments.user_id");
                        $stmt3->bindparam(":bid", $bid);
                        $stmt3->execute();
                        $comments = $stmt3->fetchAll();
                        $stmt3->closeCursor();

                        $stmt4 = $auth_user->runQuery("SELECT SUM(likes) AS sum FROM likes WHERE blog_id = :bid");
                        $stmt4->bindparam(":bid", $bid);
                        $stmt4->execute();
                        $likes = $stmt4->fetch();
                        $stmt4->closeCursor();
                        ?>
                        <form method = "POST">
                            <input type="hidden" name="blog_id" value="<?php echo $bid; ?>"/>
                            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>"/>
                            <button type="submit" class="btn btn-primary" name="btn-like">
                                Like 
                            </button>
                        </form>
                        <?php
                        echo "<strong>Likes: </strong>" . $likes['sum'];
                        ?>
                        <hr>
                        <?php
                        foreach ($comments as $comment):
                            echo "<p><img src ='profile_pictures/" . $comment["profile_picture"] . "' height=25 width = 25 >" . "<strong>" . $comment['User_name'] . "</strong>" . "</p>";
                            ?>
                            <p style="text-align: justify"><?php echo $comment['comment'] ?></p>
                            <p style="color: grey"><span class="glyphicon glyphicon-time"></span><?php echo $comment["date"] ?></p><hr>
                            <?php
                        endforeach;
                        $check = $blog['comment_on_off'];
                        if ($check == 1){
                        ?>
                        <form method="post" class="form-delete">
                            <?php
                            echo "<img src ='profile_pictures/" . $userRow["profile_picture"] . "' height=25 width = 25 >";
                            ?>
                            <!--<input type="hidden" name="userId" value="<?php echo $user_id; ?>"/>-->
                            <input type="hidden" name="blogId" value="<?php echo $blog["Blog_id"]; ?>"/>
                            <input type="text" name="comment" placeholder="Leave a comment"/>
                            <button type="submit" class="btn btn-primary" name="btn-comment">
                                Post
                            </button>
                        </form>
                        <hr>
                        <?php
                        }       
                    endforeach;
                    ?>

                </div>

                <!-- Blog Sidebar Widgets Column -->
                <div class="col-md-4">

                    <!-- Blog Search Well -->
                    <div class="well">
                        <h4>Blog Search</h4>
                        <div class="input-group">
                            <form method="POST" action="search2.php">
                                <input type="text" class="form-control" name="search" placeholder="Type in a Keyword or tag">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button-search">
                                        <span class="glyphicon glyphicon-search"></span>
                                    </button>
                                </span>
                            </form>
                        </div>
                        <!-- /.input-group -->
                    </div>

                    <!-- Blog Categories Well -->
                    <div class="well">
                        <h4>Blog Categories</h4>
                        <div class="row">
                            <div class="col-lg-6">
                                <ul class="list-unstyled">
                                    <?php
                                    foreach ($category_names as $category_name):
                                        echo "<li><a href='home.php?category=" . $category_name['category_name'] . "'>" . $category_name["category_name"] . "</a> </li>";
                                    endforeach;
                                    ?>
                                </ul>

                            </div>
                            <!-- /.col-lg-6 -->
                            <div class="col-lg-6">
                                <ul class="list-unstyled">
                                    <!--next row-->
                                </ul>
                            </div>
                            <!-- /.col-lg-6 -->
                        </div>
                        <!-- /.row -->
                    </div>


                    <!-- Side Widget Well -->


                </div>

            </div>
            <!-- /.row -->

            <hr>

            <!-- Footer -->
            <footer>
                <div class="row">
                    <div class="col-lg-12">
                        <p>Copyright &copy; Your BlogInterest 2017</p>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </footer>

        </div>
        <!-- /.container -->

        <!-- jQuery -->
        <script src="js/jquery.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="js/bootstrap.min.js"></script>

    </body>

</html>
