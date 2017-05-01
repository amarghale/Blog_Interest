<?php
require_once("session.php");

require_once("class.user.php");


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

$stmt = $auth_user->runQuery("SELECT User_name, User_id FROM users");
$stmt->execute();
$users1 = $stmt->fetchAll();
$stmt->closeCursor();

$search = filter_input(INPUT_POST, "search");
if ($search == NULL) {
    include("home.php");
    exit();
} else {
    $remove_spaces = preg_replace('/\s+/', '', $search);;
    $query = "SELECT * FROM blogs WHERE blogs.tags LIKE '%" . $remove_spaces . "%'";
    $statement = $auth_user->runQuery($query);
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
                <div class="col-md-8">


                    <?php ?>

                    <?php
                    foreach ($reults as $result):
                        $uid = $result["User_id"];
                        ?>

                        <h2>
                            <?php echo $result["blog_title"] ?>
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
                        <p><span class="glyphicon glyphicon-time"></span> Posted on <?php echo $result["blog_date"] ?></p>
                        <hr>
                        <?php echo "<img src ='uploads/" . $result["blog_image"] . "' height=300 width = 755 >" ?>
                        <hr>
                        <p style="text-align: justify"><strong>Tags: </strong> <?php echo $result["tags"] ?></p>
                        <hr>
                        <p style="text-align: justify"><?php echo $result["Blog"] ?></p>
                        <hr>
                        <?php
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
                                <input type="text" class="form-control" placeholder="Type in a Keyword or tag">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button-search">
                                        <span class="glyphicon glyphicon-search"></span>
                                    </button>
                                </span>
                            </form>
                        </div>
                        <!-- /.input-group -->
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
