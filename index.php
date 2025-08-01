<?php
session_start();

// Prevent browser from caching this page (except for bfcache, which requires JS to handle)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

if(isset($_SESSION['username']))
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
<body>
    <div id="login-container">
        <img src="assets/images/logo.png" alt="Logo" class="logo">
        <?php
            include 'includes/alerts.php';
        ?>
        <div id="login-form-container" class="form-wrapper">
            <form action="auth/login.php" method="POST">
                <input type="text" name="username" placeholder="Username" class="form-input" required>
                <input type="password" name="password" placeholder="Password" class="form-input" required>
                <input type="submit" class="submit-button" value="Log In">
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