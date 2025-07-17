<?php
include '../includes/db.php';

if(!$conn) {
    echo "Error ----> Connecting to database <br/>";
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fname=$_POST["Fname"];
    $uname=$_POST["Username"];
    $pass=$_POST["Password"];
    $email=$_POST["Email"];

    $hashedPassword = password_hash($pass,PASSWORD_BCRYPT);

    // Check if username or email exists
    $username_check = mysqli_query($conn, "SELECT * FROM users WHERE `Username` = '$uname'");
    $email_check = mysqli_query($conn, "SELECT * FROM users WHERE `Email ID` = '$email'");

    if (mysqli_num_rows($username_check) > 0 && mysqli_num_rows($email_check) > 0) {
        echo "<script>
            alert('Both username and email already exist. Account was not created.');
            window.location.href = 'register-form.php';
        </script>";
    } elseif (mysqli_num_rows($username_check) > 0) {
        echo "<script>
            alert('Username already exists. Please choose another.');
            window.location.href = 'register-form.php';
        </script>";
    } elseif (mysqli_num_rows($email_check) > 0) {
        echo "<script>
            alert('Email already exists. Please use a different one.');
            window.location.href = 'register-form.php';
        </script>";
    } else {
        // If both are unique, insert the new user
        $sql="INSERT INTO `users` (`Full Name`, `Username`, `Password`, `Email ID`) VALUES ('$fname', '$uname', '$hashedPassword', '$email')";
        $res=mysqli_query($conn,$sql);
        
        if($res) {
            echo "<script>
                alert('Account has been created successfully!  Login to enter..');
                window.location.href = '../index.php';
                </script>";
        } else {
            echo "<script>
                alert('Something went wrong. Please try again later.');
                window.location.href = 'register-form.php';
                </script>";
        }
    }
} else {
    echo " Error! ";
}

?>
