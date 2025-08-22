<?php
session_start();

require_once "../includes/db.php";
require_once "../includes/csrf.php";
require_once "../includes/alerts.php";
require_once "../includes/database_utils.php";

ensureFreshCsrfToken(); // Keeps rotating the token every 15 minutes

// Prevent caching for security
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Redirect if user not authenticated
if(!isset($_SESSION['username']) || !isset($_SESSION['userid'])) {
    header('Location:../index.php');
    exit();
}

$userId = $_SESSION['userid'];

// Handle form submission for note update
if($_SERVER['REQUEST_METHOD'] == "POST") {

    // CSRF token validation
    if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Security token mismatch. Please try again.";
        header('Location:../dashboard.php');
        exit();
    }

    $noteTitle = trim($_POST['title']);
    $noteDescription = trim($_POST['description']);
    $noteId = intval($_POST['note_id']);

    if(empty($noteTitle) || empty($noteDescription)) {
        $_SESSION['error'] = "Title and Description required";
        header('Location: edit.php?edit=' . $noteId);
        exit();
    } else {

        // Update note in database
        $updateNote = safe_prepare($dbConnection, 'UPDATE notes set Title = ?, Description = ? where note_id = ? and user_id = ?', 'edit.php?edit=' . $noteId);
        $updateNote->bind_param("ssis", $noteTitle, $noteDescription, $noteId, $userId);
        safe_execute($updateNote, 'edit.php?edit=' . $noteId);
        
        // Check if a row was actually updated
        if($updateNote->affected_rows() > 0) {
            $_SESSION['success'] = "Note updated successfully.";
        } else {
            // This handles the case where the note_id doesn't belong to the user
            $_SESSION['error'] = "Note not found or you don't have permission to edit it.";
        }
        $updateNote->close();
        header('Location:../dashboard.php');
        exit();
    }
}

$noteData = null;

// Fetch note data for editing (GET request)
if(isset($_GET['edit'])) {
    
    $noteId = intval($_GET['edit']);  // sanitize user input

    $selectNote = safe_prepare($dbConnection, 'SELECT * FROM notes WHERE note_id = ? AND user_id = ?', 'edit.php?edit=' . $noteId);
    $selectNote->bind_param("is", $noteId, $userId);
    safe_execute($selectNote, 'edit.php?edit=' . $noteId);
    $fetchNote = $selectNote->get_result();

    $noteData = $fetchNote->fetch_assoc();  // safely fetch result
    $selectNote->close();

    if(!$noteData){
        header('Location:../dashboard.php');
        exit();
    }

} else {
    header('Location:../dashboard.php');
    exit();
}
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
        if($noteData) {
            echo  "<form action='edit.php' method='POST' class='edit-form'>
                    <input type='hidden' name='note_id' value='" . $noteData['note_id'] . "'>
                    <input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>
                    <label for='title'>Title:</label>
                    <input type='text' name='title' id='title' value='" . htmlspecialchars($noteData['Title']) . "'>
                    <label for='description'>Description:</label>
                    <textarea name='description' id='description' rows='4'>" . htmlspecialchars($noteData['Description']) . "</textarea>
                    <div class='form-actions'>
                    <a href='../dashboard.php' class='cancel-link'>Cancel</a>
                    <button type='submit'>Update Note</button>
                    </div>
                    </form>";
        }
    ?>
</body>
<script>
    // Reload page on back-forward cache to prevent stale data
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
</script>
</html>