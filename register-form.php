<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up &bull; Inotes</title>
    <link rel="icon" href="../assets/images/favicon.png">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <div id="signup-container">
        <h3>Welcome to SIGN UP Page</h3>
        <?php
            if (isset($_SESSION['auth_error'])) {
                echo "<div class='error-message'>" . htmlspecialchars($_SESSION['auth_error']) . "</div>";
                unset($_SESSION['auth_error']);
            }
        ?>
        <div id="signup-form-container" class="form-wrapper">
            <form action="register.php" method="POST">
                <input type="email" id="email" name="email" placeholder="Email" class="form-input" required>
                <input type="password" id="password" name="password" placeholder="Password" class="form-input" required>
                <input type="text" id="fullname" name="fullname" placeholder="Fullname" class="form-input" required>
                <input type="text" id="username" name="username" placeholder="Username" class="form-input" required>
                <button type="submit"><b>Sign Up</b></button>
            </form>
        </div>
        <div>
            <p>Have an Account? <a href="../index.php"><b>Log in</b></a></p>
        </div>
    </div>
</body>
</html>