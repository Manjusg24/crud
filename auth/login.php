<?php
session_start();

include '../includes/db.php';

if(isset($_POST["submit"])) {
    $name=$_POST["uname"];
    $pass=$_POST["pass"];

    // Prepare the SQL query with a placeholder
    $stmt = $conn->prepare("Select * from `users` where Username = ?"); 
    
    // Bind the actual username value to the placeholder
    $stmt->bind_param("s", $uname); // "s" means string

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();
    
    // Check if the user exists
    if($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify the password
        if(password_verify($pass,$user['Password'])) {
            $_SESSION['name'] = $user['Username'];
            header("Location: ../dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "&#x274c Password incorrect.";
            header("Location: ../index.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "&#x274c Invalid username.";
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
