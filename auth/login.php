<?php
session_start();

require_once '../includes/db.php';
require_once '../includes/csrf.php';
require_once '../includes/database_utils.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Security token mismatch. Please try again.";
        header('Location:../index.php');
        exit();
    }

    // Trim and normalize inputs
    $inputUsername = strtolower(trim($_POST['username']));
    $inputPassword = $_POST['password'];

    // Server-side input validation
    if(empty($inputUsername) || empty($inputPassword)) {
        $_SESSION['error'] = "Both username and password are required.";
        header('Location:../index.php');
        exit();
    }

    // Prepare the SQL query with a placeholder
    $userLookup = safe_prepare($dbConnection, 'Select * from `users` where Username = ?', 'index.php');
    
    // Bind the actual username value to the placeholder
    $userLookup->bind_param('s',$inputUsername); // "s" means string

    // Execute the query
    safe_execute($userLookup, 'index.php');

    // Get the result
    $userResult = $userLookup->get_result();
    
    // Check if the user exists
    if($userResult->num_rows === 1) {
        $userRecord = $userResult->fetch_assoc();
        
        // Verify the password
        if(password_verify($inputPassword,$userRecord['Password'])) {

            session_regenerate_id(true); // Regenerate session ID to prevent fixation attacks

            $_SESSION['username'] = $userRecord['Username'];
            $_SESSION['userid'] = $userRecord['user_id'];

            regenerateCsrfToken();  // Regenerate fresh token after successful login

            header('Location: ../dashboard.php');
            exit();
        } else {
            $_SESSION['error'] = "Password incorrect";
            header('Location: ../index.php');
            exit();
        }

    } else {
        $_SESSION['error'] = "Invalid username";
        header('Location: ../index.php');
        exit();
    }

    // Close the statement
    $userLookup->close();
} else {
    header('Location: ../index.php');
    exit();
}

?>