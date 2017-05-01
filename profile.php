<?php
require_once("session.php");

require_once("class.user.php");
require_once ("constants.php");
$auth_user = new USER();


$user_id = $_SESSION['user_session'];

if ($auth_user->isLoginSessionExpired()) {
    session_destroy();
    $auth_user->redirect('index.php');
}
$stmt = $auth_user->runQuery("SELECT * FROM users WHERE User_id=:user_id");
$stmt->execute(array(":user_id" => $user_id));

$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();

$stmt1 = $auth_user->runQuery("SELECT * FROM blogs WHERE User_id = $user_id");
$stmt1->execute();
$blogs = $stmt1->fetchAll();
$stmt1->closeCursor();


if (isset($_POST['btn-upload'])) {
    $target_dir = "profile_pictures/";
    $target_file = $target_dir . basename($_FILES["txt_file"]["name"]);
    $uploadOk = 1;
    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

    $check = getimagesize($_FILES["txt_file"]["tmp_name"]);
    if ($check !== false) {
        $msg[] = "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        $msg[] = "File is not an image.";
        $uploadOk = 0;
    }
    // Checks if file already exists
    if (file_exists($target_file)) {
        $msg[] = "Sorry, file already exists.";
        $uploadOk = 0;
    }
// Checks file size
    $file_size = constants::getFileSIZE();
    if ($_FILES["txt_file"]["size"] > $file_size) {
        $msg[] = "Sorry, your file is too large.";
        $uploadOk = 0;
    }
// Allows certain file formats
    $file_type = constants::getFileType();
    if ($imageFileType != $file_type) {
        $msg[] = "Sorry, PNG files are allowed.";
        $uploadOk = 0;
    }
    if ($uploadOk = 0) {
        $msg[] = "Sorry Your file was not uploaded";
    } else {
        $image = $_FILES['txt_file']['name'];
    }
    $auth_user->upload_avatar($image, $user_id);
    if (move_uploaded_file($_FILES["txt_file"]["tmp_name"], $target_file)) {
        $msg[] = "The file " . basename($_FILES["image"]["name"]) . " has been uploaded.";
        $auth_user->redirect('profile.php');
    }
}

$stmt2 = $auth_user->runQuery("SELECT profile_picture FROM users WHERE User_id = $user_id");
$stmt2->execute();
$picture = $stmt2->fetch();
$stmt2->closeCursor();

if (isset($_POST['btn-delete'])) {
    $blog_id = strip_tags($_POST['blogId']);
    if ($auth_user->delete_blog($blog_id)) {
        $auth_user->redirect('profile.php');
    }
}
if (isset($_POST['submit'])) {
    $switch = strip_tags($_POST['trigger']);
    $bid = strip_tags($_POST['bid']);
    if($auth_user->enable_disable_comments($switch, $user_id, $bid)){
        $auth_user->redirect("profile.php");
    }
    
    
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen"> 
        <link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" media="screen"> 
        <script type="text/javascript" src="jquery-1.11.3-jquery.min.js"></script>
        <link rel="stylesheet" href="css/style.css" type="text/css"  />
        <title>welcome - <?php echo ($userRow['User_name']); ?></title>

    </head>

    <body>


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
                    <!--                    <ul class="nav navbar-nav">
                                            <li class="active"><a href="">Back to Article</a></li>
                                            <li><a href="">Next</a></li>
                                            <li><a href="">Previous</a></li>
                                        </ul>-->
                    <ul class="nav navbar-nav navbar-right">

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <span class="glyphicon glyphicon-user"></span>&nbsp;Hi' <?php echo $userRow['email']; ?>&nbsp;<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#"><span class="glyphicon glyphicon-user"></span>&nbsp;View Profile</a></li>
                                <li><a href="logout.php?logout=true"><span class="glyphicon glyphicon-log-out"></span>&nbsp;Sign Out</a></li>
                            </ul>
                        </li>
                    </ul>
                </div><!--/.nav-collapse -->

            </div>
        </nav>

        <div class="clearfix"></div>

        <div class="container-fluid" style="margin-top:80px;">

            <div class="container">

                <label class="h5">welcome : <?php
print($userRow['User_name']);
echo "<img src ='profile_pictures/" . $picture["profile_picture"] . "' height=50 width = 50 >"
?></label>
                <hr />
                <h1>
                    <a href="home.php"><span class="glyphicon glyphicon-home"></span> home</a> &nbsp; 
                    <a href="new_blog.php"><span class="glyphicon glyphicon-upload"></span> Post Blog</a></h1>

                <form method="post" class="form-signin" enctype="multipart/form-data" >
                    <?php
                    if (isset($msg)) {
                        foreach ($msg as $msg) {
                            ?>
                            <div class="alert alert-danger">
                                <i class="glyphicon glyphicon-warning-sign"></i> &nbsp; <?php echo $msg; ?>
                            </div>
                            <?php
                        }
                    }
                    ?>
                    <div class="form-group">
                        <input type="file" class="form-control" name="txt_file" value=" 
                        <?php
                        if (isset($error)) {
                            echo $image;
                        }
                        ?>" />
                    </div>
                    <div class="clearfix"></div><hr />
                    <div class="form-grousp">
                        <button type="submit" class="btn btn-primary" name="btn-upload">
                            <i class="glyphicon glyphicon-open-file"></i>&nbsp;Upload Avatar
                        </button>
                    </div>
                </form>
                <hr />

                <?php
                foreach ($blogs as $blog):
                    ?>

                    <h2>
                        <?php echo $blog["blog_title"] ?>
                    </h2>
                    <p class="lead">
                        by <a href="index.php"><?php
                        echo $userRow["User_name"];
                        ?>
                        </a>
                    <form method ="POST" >
                        <Input type = 'Radio' Name ='trigger' value= '1'
                               >On
                        <Input type = 'Radio' Name ='trigger' value= '0' 
                               >Off
                        <input type="hidden" name="bid" value="<?php echo $blog['Blog_id'];?>">
                        <button type="submit" class="btn btn-primary" name="submit">
                            Enable/Disable Comments
                        </button>
                    </form>
                    </p>                          
                    <p><span class="glyphicon glyphicon-time"></span> Posted on <?php echo $blog["blog_date"] ?></p>
                    <hr>
                    <?php echo "<img src ='uploads/" . $blog["blog_image"] . "' height=300 width = 755 >" ?>
                    <hr>
                    <p style="text-align: justify"><strong>Tags: </strong> <?php echo $blog["tags"] ?></p>
                    <hr>
                    <p style="text-align: justify"><?php echo $blog["Blog"] ?></p>
                    <!--<a class="btn btn-primary" href="#">Read More <span class="glyphicon glyphicon-chevron-right"></span></a>-->
                    <form method="post" class="form-delete" >
                        <input type="hidden" name="blogId" value="<?php echo $blog["Blog_id"]; ?>"/>
                        <button type="submit" class="btn btn-primary" name="btn-delete">
                            <i class="glyphicon glyphicon-minus-sign"></i>&nbsp;Delete Blog
                        </button>
                    </form>
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
                    endforeach;
                    ?>


            </div>
            <script src="bootstrap/js/bootstrap.min.js"></script>

    </body>
</html>