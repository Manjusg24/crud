<?php
session_start();

require_once '../includes/db.php';
require_once '../includes/database_utils.php';
require_once '../includes/alerts.php';

if(!$dbConnection) {
    $_SESSION['error'] = "Something went wrong. Please try again later.";
    header('Location: register-form.php');
    exit();
}

// Generate Unique UserId
do {
    $userId = bin2hex(random_bytes(4));
    $check = mysqli_query($dbConnection,"SELECT 1 FROM users WHERE user_id = '$userId'");
} while($check->num_rows > 0);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST["fullname"]);
    $username = strtolower(trim($_POST["username"]));
    $password = $_POST["password"];
    $email = strtolower(trim($_POST["email"]));

    // Server-side validation
    if (empty($fullname) || empty($username) || empty($password) || empty($email)) {
        $_SESSION['error'] = "All fields are required to register.";
        header('Location: register-form.php');
        exit();
    }

    // Enforces strong password
    if(
        strlen($password) < 8 ||
        !preg_match("/[a-z]/",$password) || // at least one lowercase letter
        !preg_match("/[A-Z]/",$password) || // at least one uppercase letter
        !preg_match("/[0-9]/",$password) || // at least one number
        !preg_match("/[\W]/",$password)     // at least one special character
      ) {
        $_SESSION['error'] = "Password must be atleast 8 characters long and include uppercase, lowercase, number and special character.";
        header('Location: register-form.php');
        exit();
    }
    
    $hashedPassword = password_hash($password,PASSWORD_BCRYPT);

    // Check if username exists
    $checkUsername = safe_prepare($dbConnection,"SELECT * FROM users WHERE `Username` = ?");
    $checkUsername->bind_param("s", $username);
    safe_execute($checkUsername);
    $usernameResult = $checkUsername->get_result();

    // Check if email exists
    $checkEmail = safe_prepare($dbConnection,"SELECT * FROM users WHERE `Email ID` = ?");
    $checkEmail->bind_param("s", $email);
    safe_execute($checkEmail);
    $emailResult = $checkEmail->get_result();

    if ($usernameResult->num_rows > 0 && $emailResult->num_rows > 0) {
        $_SESSION['error'] = "Both username and email already exist. Account was not created";
        header('Location: register-form.php');
        exit();
    } elseif ($usernameResult->num_rows > 0) {
        $_SESSION['error'] = "Username already exists. Please choose another";
        header('Location: register-form.php');
        exit();
    } elseif ($emailResult->num_rows > 0) {
        $_SESSION['error'] = "Email already exists. Please use a different one";
        header('Location: register-form.php');
        exit();
    } else {
        // If both are unique, insert the new user
        $insertUser = safe_prepare($dbConnection,"INSERT INTO `users` (`full_name`, `username`, `password`, `email_id`, `user_id`) VALUES (?, ?, ?, ?, ?)");
        $insertUser->bind_param("sssss", $fullname, $username, $hashedPassword, $email, $userId);

        safe_execute($insertUser);
        
        $_SESSION['success'] = "Account created successfully! You can login..";
        header('Location: ../index.php');
        exit();
        
        $insertUser->close();
    }
    $checkUsername->close();
    $checkEmail->close();
} else {
    header('Location: register-form.php');
    exit();
}

?>