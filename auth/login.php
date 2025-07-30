<?php
session_start();

include '../includes/db.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Trim and normalize inputs
    $username = strtolower(trim($_POST["username"]));
    $password = $_POST["password"];

    // Server-side input validation
    if(empty($username) || empty($password)) {
        $_SESSION['auth_error'] = "Both username and password are required.";
        header("location:../index.php");
        exit();
    }

    // Prepare the SQL query with a placeholder
    $stmt = $conn->prepare("Select * from `users` where Username = ?"); 
    
    // Bind the actual username value to the placeholder
    $stmt->bind_param('s',$username); // "s" means string

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();
    
    // Check if the user exists
    if($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify the password
        if(password_verify($password,$user['Password'])) {
            $_SESSION['username'] = $user['Username'];
            header("Location: ../dashboard.php");
            exit();
        } else {
            $_SESSION['auth_error'] = "Password incorrect";
            header("Location: ../index.php");
            exit();
        }

    } else {
        $_SESSION['auth_error'] = "Invalid username";
        header("Location: ../index.php");
        exit();
    }

    // Close the statement
    $stmt->close();
} else {
    header("Location: ../index.php");
    exit();
}

?>