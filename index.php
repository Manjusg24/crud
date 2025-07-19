<?php
session_start();

// Prevent browser from caching this page (except for bfcache, which requires JS to handle)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

if(isset($_SESSION['name']))
{
    header("Location:dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inotes</title>
    <link rel="icon" href="assets/images/favicon.png">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body class="login-page">
    <div id="id">
        <img src="assets/images/logo.png" alt="Logo" class="logo">
        <?php
            if (isset($_SESSION['error'])) {
                echo "<div class='error-message'>".$_SESSION['error']."</div>";
                unset($_SESSION['error']);
            }
        ?>
        <div id="myid" class="myclass">
            <form action="auth/login.php" method="POST" id="myform">
                <input type="text" name="uname" placeholder="Username" class="myclass" required>
                <input type="password" name="pass" placeholder="Password" class="myclass" required>
                <input type="submit" name="submit" class="myclass" value="Log In">
            </form>
        </div>
        <div>
            <p>Don't have an Account? <a href="auth/register-form.php"><b>Sign up</b></a></p>
        </div>
    </div>
</body>
<script>
    // Works even with bfcache (Back-Forward Cache)
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
</script>
</html>
