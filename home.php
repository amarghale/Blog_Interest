<?php
require_once("session.php");

require_once("class.user.php");
$auth_user = new USER();


$user_id = $_SESSION['user_session'];

$stmt = $auth_user->runQuery("SELECT * FROM users WHERE User_id=:user_id");
$stmt->execute(array(":user_id" => $user_id));

$userRow = $stmt->fetch(PDO::FETCH_ASSOC);

$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["txt_file"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);


if (isset($_POST['btn-post'])) {
    $title = strip_tags($_POST['txt_title']);
    $blog = strip_tags($_POST['txt_blog']);

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
    if ($_FILES["txt_file"]["size"] > 500000) {
        $msg[] = "Sorry, your file is too large.";
        $uploadOk = 0;
    }
// Allows certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $msg[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    if ($uploadOk = 0) {
        $msg[] = "Sorry Your file was not uploaded";
    } else {
        $image = $_FILES['txt_file']['name'];
    }
    $auth_user->post_blog($title, $blog, $user_id, $image);

    if (move_uploaded_file($_FILES["txt_file"]["tmp_name"], $target_file)) {
        $msg[] = "The file " . basename($_FILES["image"]["name"]) . " has been uploaded.";
        $auth_user->redirect('home.php');
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
        <title>welcome - <?php print($userRow['user_email']); ?></title>
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
                    <a class="navbar-brand" href="">Blog</a>
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


        <div class="clearfix"></div>


        <div class="container-fluid" style="margin-top:80px;">

            <div class="container">

                <label class="h5">welcome : <?php print($userRow['User_name']); ?></label>
                <hr />

                <h1>
                    <a href="home.php"><span class="glyphicon glyphicon-home"></span> home</a> &nbsp; 
                    <a href="profile.php"><span class="glyphicon glyphicon-user"></span> profile</a></h1>
                <hr />

                <p class="h4">User Home Page</p> 
                <form method="post" class="form-signin" enctype="multipart/form-data" >
                    <h2 class="form-signin-heading">Post New Blog.</h2><hr />
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
                        <input type="text" class="form-control" name="txt_title" placeholder="Enter Blog Title"/>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" name="txt_blog" placeholder="Enter Blog" cols="50" rows="5"></textarea>   
                    </div>
                    <div class="form-group">
                        <input type="file" class="form-control" name="txt_file"/>
                    </div>
                    <div class="clearfix"></div><hr />
                    <div class="form-grousp">
                        <button type="submit" class="btn btn-primary" name="btn-post">
                            <i class="glyphicon glyphicon-open-file"></i>&nbsp;POST
                        </button>
                    </div>
                    <br />
                </form>
            </div>
        </div>

    </div>

    <script src="bootstrap/js/bootstrap.min.js"></script>

</body>
</html>