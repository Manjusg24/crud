<?php
include "../includes/db.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Note</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="icon" href="../assets/images/favicon.png">
</head>
<body>
    <?php
     if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $edit = intval($_POST['note_id']);

        if(empty($title) || empty($description)) {
            echo "Title and Description required";
        } else {
            $updateQuery = $conn->prepare("UPDATE notes set Title = ?, Description = ? where note_id = ?");
            $updateQuery->bind_param("ssi",$title,$description,$edit);
            
            if($updateQuery->execute()) {
                header("location:../dashboard.php");
                exit();
            } else {
                echo "Error updating note";
            }
        }
    }
    if(isset($_GET['edit'])) {
        
        $edit = intval($_GET['edit']);  // sanitize user input

        // use prepared statement
        $sql = $conn->prepare("select * from notes where note_id=?");
        $sql->bind_param("i",$edit);
        $sql->execute();
        $res = $sql->get_result();

        $note = mysqli_fetch_assoc($res);  // safely fetch result
    
        if($note) {
            echo  "<form action='edit.php' method='POST' class='edit-form'>
            <input type='hidden' name='note_id' value='" . $edit . "'>
                    <label for='title'>Title:</label>
                    <input type='text' name='title' id='title' value='" . htmlspecialchars($note['Title']) . "'>
                    <label for='description'>Description:</label>
                    <textarea name='description' id='description' rows='4'>" . htmlspecialchars($note['Description']) . "</textarea>
                    <div class='form-actions'>
                    <a href='../dashboard.php' class='cancel-link'>Cancel</a>
                    <button type='submit'>Update Note</button>
                    </div>
                    </form>";
        } else {
            echo "Note not found!";
        }
    } else {
        echo "Invalid Request";
    }
    ?>
</body>
</html>