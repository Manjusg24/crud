<?php
include '../includes/db.php';

if(!$conn)
{
    echo "Error ----> Connecting to database <br/>";
}

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $fname=$_POST["Fname"];
    $uname=$_POST["Username"];
    $pass=$_POST["Password"];
    $email=$_POST["Email"];

    $sql="INSERT INTO `users` (`SNo`, `Full Name`, `Username`, `Password`, `Email ID`, `Mobile Number`) VALUES (1, '$fname', '$uname', '$pass', '$email', '9876054321')";
    $res=mysqli_query($conn,$sql);

    
}
else{
    echo " Error! ";
}

?>
<script>
    alert("Account has been created successfully!  Login to enter..");
</script>
