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
</html>