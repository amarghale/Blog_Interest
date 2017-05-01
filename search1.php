<?php
require_once('config/dbconfig.php');
$database = new Database();
$db = $database->dbConnection();

$stmt = $db->prepare("SELECT User_name, User_id FROM users");
$stmt->execute();
$users1 = $stmt->fetchAll();
$stmt->closeCursor();

$search = filter_input(INPUT_POST, "search");

if ($search == NULL) {
    include("index.php");
    exit();
} else {
    $query = "SELECT * FROM blogs WHERE blogs.tags LIKE '%" . $search . "%'";
    $statement = $db->prepare($query);
    $statement->execute();
    $reults = $statement->fetchAll();
    $statement->closeCursor();
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

        <title>Blog Interest</title>
        <!-- Bootstrap Core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="css/blog-home.css" rel="stylesheet">

    </head>

    <body>

        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.php">Blog Interest</a>
                </div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="#">About</a>
                        </li>
                        <li>
                            <a href="login.php">Login</a>
                        </li>
                        <li>
                            <a href="sign-up.php">Registration</a>
                        </li>
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </div>
            <!-- /.container -->
        </nav>


        <!-- Page Content -->
        <div class="container">

            <div class="row">

                <!-- Blog Entries Column -->
                <div class="col-md-8">

                    <h1 class="page-header">
                        Read Through Blogs From Your Favorite Blogger!
                        <small>Enjoy and Have Fun!</small>
                    </h1>


                    <?php ?>

                    <?php
                    foreach ($reults as $result):
                        $uid = $result["User_id"];
                        ?>

                        <h2>
                            <?php echo $result["blog_title"] ?>
                        </h2>
                        <?php
                        $stmt = $db->prepare("SELECT User_name, User_id, profile_picture FROM users WHERE User_id = $uid");
                        $stmt->execute();
                        $users = $stmt->fetchAll();
                        $stmt->closeCursor();
                        ?>
                        <?php foreach ($users as $user): ?>
                            <p class="lead">
                                by <?php
                    echo "<a href='public_profile.php?id=" . $user['User_id'] . "'>" . $user["User_name"] . "<br> <br>" . "</a>";
                    echo "<img src ='profile_pictures/" . $user["profile_picture"] . "' height=50 width = 50 >";
                endforeach;
                        ?>
                            </a>
                        </p>                          
                        <p><span class="glyphicon glyphicon-time"></span> Posted on <?php echo $result["blog_date"] ?></p>
                        <hr>
                        <?php echo "<img src ='uploads/" . $result["blog_image"] . "' height=300 width = 755 >" ?>
                        <hr>
                        <p style="text-align: justify"><strong>Tags: </strong> <?php echo $result["tags"] ?></p>
                        <hr>
                        <p style="text-align: justify"><?php echo $result["Blog"] ?></p>
                        <hr>
                        <?php
                        $bid = $result['Blog_id'];
                        $stmt3 = $db->prepare("SELECT * FROM comments, users WHERE comments.Blog_id = :bid AND users.User_id = comments.user_id");
                        $stmt3->bindparam(":bid", $bid);
                        $stmt3->execute();
                        $comments = $stmt3->fetchAll();
                        $stmt3->closeCursor();

                        $stmt4 = $db->prepare("SELECT SUM(likes) AS sum FROM likes WHERE blog_id = :bid");
                        $stmt4->bindparam(":bid", $bid);
                        $stmt4->execute();
                        $likes = $stmt4->fetch();
                        $stmt4->closeCursor();
                        echo "<strong>Likes: </Strong>" . $likes['sum'];
                        echo "<hr>";
                        foreach ($comments as $comment):
                            ?>
                            <?php
                            echo "<p><img src ='profile_pictures/" . $comment["profile_picture"] . "' height=25 width = 25 >" . "<strong>" . $comment['User_name'] . "</strong>" . "</p>";
                            ?>
                            <p style="text-align: justify"><?php echo $comment['comment'] ?></p>
                            <p style="color: grey"><span class="glyphicon glyphicon-time"></span><?php echo $comment["date"] ?></p><hr>
                            <?php
                        endforeach;
                    endforeach;
                    ?>

                </div>

                <!-- Blog Sidebar Widgets Column -->
                <div class="col-md-4">

                    <!-- Blog Search Well -->
                    <div class="well">
                        <h4>Blog Search</h4>
                        <div class="input-group">
                            <form method="POST" action="search1.php">
                                <input type="text" class="form-control" placeholder="Type in a Keyword or tag">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button-search">
                                        <span class="glyphicon glyphicon-search"></span>
                                    </button>
                                </span>
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
//                                    foreach ($category_names as $category_name):
//                                        echo "<li><a href='index.php?category=" . $category_name['category_name'] . "'>" . $category_name["category_name"] . "</a> </li>";
//                                    endforeach;
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
                    <div class="well">
                        <h4>Popular Bloggers</h4>

                        <?php
                        foreach ($users1 as $user):

                            echo "<a href='public_profile.php?id=" . $user['User_id'] . "'>" . $user["User_name"] . "<br> <br>" . "</a>";
                        endforeach;
                        ?>
                    </div>

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
