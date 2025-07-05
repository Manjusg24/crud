<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up &bull; Inotes</title>
    <link rel="icon" href="../assets/images/favicon.png">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body class="register-page">
    <div id="id">
        <h3>Welcome to SIGN UP Page</h3>
        <div id="myid">
            <form action="register.php" method="POST">
                <input type="email" id="Email" name="Email" placeholder="Email" class="myclass" required>
                <input type="password" id="Password" name="Password" placeholder="Password" class="myclass" required>
                <input type="text" id="Fname" name="Fname" placeholder="Fullname" class="myclass" required>
                <input type="text" id="Username" name="Username" placeholder="Username" class="myclass" required>
                <button type="submit"><b>Sign Up</b></button>
            </form>
            
        </div>
        <div>
            <p>Have an Account? <a href="../index.php"><b>Log in</b></a></p>
        </div>
    </div>
</body>
</html>