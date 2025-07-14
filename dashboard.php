<?php
session_start();

if(!isset($_SESSION['name']))
{
    header("location:index.php");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="icon" href="assets/images/favicon.png">
    <link rel="stylesheet" href="assets/css/dashboard.css"></link>
</head>
<body>
    <h1><?php echo "Hi " . $_SESSION['name'] . ", You're logged in.." ?></h1>
    <h3><a href="auth/logout.php">Click to logout</a></h3>
</body>
</html>