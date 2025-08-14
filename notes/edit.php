<?php
include "../includes/db.php";

session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

if(!isset($_SESSION['username'])) {
    header("location:../index.php");
    exit();
}

$userId = $_SESSION['userid'];
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
        $noteTitle = trim($_POST['title']);
        $noteDescription = trim($_POST['description']);
        $noteId = intval($_POST['note_id']);

        if(empty($noteTitle) || empty($noteDescription)) {
            echo "Title and Description required";
        } else {
            $updateNote = $conn->prepare("UPDATE notes set Title = ?, Description = ? where note_id = ? and user_id = ?");
            $updateNote->bind_param("ssis",$noteTitle,$noteDescription,$noteId,$userId);
            
            if($updateNote->execute()) {
                header("location:../dashboard.php");
                exit();
            } else {
                echo "Error updating note";
            }
        }
    }
    if(isset($_GET['edit'])) {
        
        $noteId = intval($_GET['edit']);  // sanitize user input

        // use prepared statement
        $selectNote = $conn->prepare("SELECT * FROM notes WHERE note_id = ? AND user_id = ?");
        $selectNote->bind_param("is",$noteId,$userId);
        $selectNote->execute();
        $fetchNote = $selectNote->get_result();

        $noteData = mysqli_fetch_assoc($fetchNote);  // safely fetch result
    
        if($noteData) {
            echo  "<form action='edit.php' method='POST' class='edit-form'>
                    <input type='hidden' name='note_id' value='" . $noteId . "'>
                    <label for='title'>Title:</label>
                    <input type='text' name='title' id='title' value='" . htmlspecialchars($noteData['Title']) . "'>
                    <label for='description'>Description:</label>
                    <textarea name='description' id='description' rows='4'>" . htmlspecialchars($noteData['Description']) . "</textarea>
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
<script>
    // Works even with bfcache (Back-Forward Cache)
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
</script>
</html>