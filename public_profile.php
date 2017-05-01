<?php
require_once('config/dbconfig.php');
$database = new Database();
$db = $database->dbConnection();

$user_id = $_GET['id'];

$stmt = $db->prepare("SELECT * FROM users WHERE User_id = :user_id ");
$stmt->execute(array(":user_id" => $user_id));
$users1 = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();


$stmt1 = $db->prepare("SELECT * FROM blogs WHERE blogs.User_id = $user_id");
$stmt1->execute();
$blogs = $stmt1->fetchAll();
$stmt1->closeCursor();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen"> 
        <link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" media="screen"> 
        <script type="text/javascript" src="jquery-1.11.3-jquery.min.js"></script>
        <link rel="stylesheet" href="css/style.css" type="text/css"  />
        <title>User Profile</title>
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
        <div class="clearfix"></div>

        <div class="container-fluid" style="margin-top:80px;">

            <div class="container">

                <label class="h5">Welcome To <?php print($users1['User_name']); ?>'s Profile</label>
                <hr />

                <h1>
                    <a href="index.php"><span class="glyphicon glyphicon-home"></span> home</a> &nbsp; 
                    <hr />
                    <?php
                    foreach ($blogs as $blog):
                        $uid = $blog["User_id"];
                        ?>

                        <h2>
                            <?php echo $blog["blog_title"] ?>
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
                        <p><span class="glyphicon glyphicon-time"></span> Posted on <?php echo $blog["blog_date"] ?></p>
                        <hr>
                        <?php echo "<img src ='uploads/" . $blog["blog_image"] . "' height=300 width = 755 >" ?>
                        <hr>
                        <p style="text-align: justify"><?php echo $blog["Blog"] ?></p>

                        <hr>
                        <?php
                        $bid = $blog['Blog_id'];
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

                    </div>
                    <script src="bootstrap/js/bootstrap.min.js"></script>

                    </body>
                    </html>