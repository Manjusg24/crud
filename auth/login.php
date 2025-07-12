<?php
session_start();

include '../includes/db.php';

if(isset($_POST["submit"])){
    $name=$_POST["uname"];
    $pass=$_POST["pass"];

    $sql = "Select * from `users` where Username='$name'"; 
    $query = mysqli_query($conn,$sql);
    $count = mysqli_num_rows($query); 
    if($count)
    {
        $fetch = mysqli_fetch_assoc($query);
        $fpass = $fetch['Password'];
        
        if(password_verify($pass,$fpass)){
            $_SESSION['name'] = $fetch['Username'];
            echo "<script> location.replace('dashboard.php'); </script>";
        }
        else{
            echo "Password Incorrect";
        }

    }
    else{
    echo "Invalid Username";
    
    }
}

?>
