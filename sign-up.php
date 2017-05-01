<?php
session_start();
require_once('class.user.php');

$user = new USER();

if ($user->is_loggedin() != "") {
    $user->redirect('home.php');
}

if (isset($_POST['btn-signup'])) {
    $fname = strip_tags($_POST['txt_fname']);
    $uname = strip_tags($_POST['txt_uname']);
    $umail = strip_tags($_POST['txt_umail']);
    $upass = strip_tags($_POST['txt_upass']);
    $upass1 = strip_tags($_POST['txt_upass1']);

    if ($uname == "") {
        $error[] = "provide username !";
    } else if ($umail == "") {
        $error[] = "provide email id !";
    } else if (!filter_var($umail, FILTER_VALIDATE_EMAIL)) {
        $error[] = 'Please enter a valid email address !';
    } else if ($upass == "") {
        $error[] = "provide password !";
    } else if (strlen($upass) < 6) {
        $error[] = "Password must be atleast 6 characters";
    } else if ($upass != $upass1) {
        $error[] = "Password must match !";
    } else {
        try {
            $stmt = $user->runQuery("SELECT User_name, email FROM users WHERE User_name=:uname OR email=:umail");
            $stmt->execute(array(':uname' => $uname, ':umail' => $umail));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row['User_name'] == $uname) {
                $error[] = "sorry username already taken !";
            } else if ($row['email'] == $umail) {
                $error[] = "sorry email id already registered!";
            } else {
                if ($user->register($fname, $uname, $umail, $upass, $upass1, $image)) {
                    $user->redirect('sign-up.php?joined');
                }
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Sign up</title>
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" media="screen">
        <link rel="stylesheet" href="css/style.css" type="text/css"  />
    </head>
    <body>
        <div class="signin-form">

            <div class="container">

                <form method="post" class="form-signin">
                    <h2 class="form-signin-heading">Sign up.</h2><hr />
                    <?php
                    if (isset($error)) {
                        foreach ($error as $error) {
                            ?>
                            <div class="alert alert-danger">
                                <i class="glyphicon glyphicon-warning-sign"></i> &nbsp; <?php echo $error; ?>
                            </div>
                            <?php
                        }
                    } else if (isset($_GET['joined'])) {
                        ?>
                        <div class="alert alert-info">
                            <i class="glyphicon glyphicon-log-in"></i> &nbsp; Successfully registered <a href='index.php'>login</a> here
                        </div>
                        <?php
                    }
                    ?>
                    <div class="form-group">
                        <input type="text" class="form-control" name="txt_fname" placeholder="Enter Full Name" value="<?php
                        if (isset($error)) {
                            echo $fname;
                        }
                        ?>" />
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="txt_uname" placeholder="Enter Username" value="<?php
                        if (isset($error)) {
                            echo $uname;
                        }
                        ?>" />
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="txt_umail" placeholder="Enter E-Mail ID" value="<?php
                        if (isset($error)) {
                            echo $umail;
                        }
                        ?>" />
                    </div>

                    <div class="form-group">
                        <input type="password" class="form-control" name="txt_upass" placeholder="Enter Password" />
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="txt_upass1" placeholder="Enter Password Again" />
                    </div>
                    <div class="clearfix"></div><hr />
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" name="btn-signup">
                            <i class="glyphicon glyphicon-open-file"></i>&nbsp;SIGN UP
                        </button>
                    </div>
                    <br />
                    <label>have an account ! <a href="login.php">Sign In</a></label> <br>
                    <label>Go back to Home ! <a href="index.php" class="glyphicon glyphicon-home">Home</a></label>

                </form>
            </div>
        </div>

    </div>

</body>
</html>