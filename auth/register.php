<?php
session_start();

include '../includes/db.php';

if(!$conn) {
    echo "Something went wrong. Please try again later.";
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST["fullname"]);
    $username = strtolower(trim($_POST["username"]));
    $password = $_POST["password"];
    $email = strtolower(trim($_POST["email"]));

    $hashedPassword = password_hash($password,PASSWORD_BCRYPT);

    // Check if username exists
    $stmt1 = $conn->prepare("SELECT * FROM users WHERE `Username` = ?");
    $stmt1->bind_param("s", $username);
    $stmt1->execute();
    $result1 = $stmt1->get_result();

    // Check if email exists
    $stmt2 = $conn->prepare("SELECT * FROM users WHERE `Email ID` = ?");
    $stmt2->bind_param("s", $email);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    if ($result1->num_rows > 0 && $result2->num_rows > 0) {
        $_SESSION['auth_error'] = "Both username and email already exist. Account was not created";
        header("Location: register-form.php");
        exit();
    } elseif ($result1->num_rows > 0) {
        $_SESSION['auth_error'] = "Username already exists. Please choose another";
        header("Location: register-form.php");
        exit();
    } elseif ($result2->num_rows > 0) {
        $_SESSION['auth_error'] = "Email already exists. Please use a different one";
        header("Location: register-form.php");
        exit();
    } else {
        // If both are unique, insert the new user
        $stmt3 = $conn->prepare("INSERT INTO `users` (`Full Name`, `Username`, `Password`, `Email ID`) VALUES (?, ?, ?, ?)");
        $stmt3->bind_param("ssss", $fullname, $username, $hashedPassword, $email);

        if($stmt3->execute()) {
            $_SESSION['success'] = "Account created successfully! You can login..";
            header("Location: ../index.php");
            exit();
        } else {
            $_SESSION['auth_error'] = "We couldn't create your account right now. Please try again shortly.";
            header("Location: register-form.php");
            exit();
        }
        $stmt3->close();
    }
    $stmt1->close();
    $stmt2->close();
} else {
    header("Location: register-form.php");
    exit();
}

?>