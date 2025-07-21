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

    // Check if username exists
    $stmt1 = $conn->prepare("SELECT * FROM users WHERE `Username` = ?");
    $stmt1->bind_param("s", $uname);
    $stmt1->execute();
    $result1 = $stmt1->get_result();

    // Check if email exists
    $stmt2 = $conn->prepare("SELECT * FROM users WHERE `Email ID` = ?");
    $stmt2->bind_param("s", $uname);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    if ($result1->num_rows() > 0 && $result2->num_rows() > 0) {
        echo "<script>
            alert('Both username and email already exist. Account was not created.');
            window.location.href = 'register-form.php';
        </script>";
    } elseif ($result1->num_rows() > 0) {
        echo "<script>
            alert('Username already exists. Please choose another.');
            window.location.href = 'register-form.php';
        </script>";
    } elseif ($result2->num_rows() > 0) {
        echo "<script>
            alert('Email already exists. Please use a different one.');
            window.location.href = 'register-form.php';
        </script>";
    } else {
        // If both are unique, insert the new user
        $stmt3 = $conn->prepare("INSERT INTO `users` (`Full Name`, `Username`, `Password`, `Email ID`) VALUES (?, ?, ?, ?)");
        $stmt3->bind_param("ssss", $fname, $uname, $epass, $email);

        if($stmt3->execute()) {
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
        $stmt3->close();
    }
    $stmt1->close();
    $stmt2->close();
} else {
    echo " Error! ";
}

?>
