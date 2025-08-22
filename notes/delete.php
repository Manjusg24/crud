<?php
session_start();

require_once "../includes/db.php";
require_once "../includes/csrf.php";
require_once "../includes/database_utils.php";
require_once "../includes/alerts.php";

ensureFreshCsrfToken(); // Keeps rotating the token every 15 minutes

if(!isset($_SESSION['userid'])) {
    header('Location:../index.php');
    exit();
}

$userId = $_SESSION['userid'];

// Handle delete request
if($_SERVER['REQUEST_METHOD'] == "POST") {

    // CSRF token validation
    if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Security token mismatch. Please try again.";
        header('Location:../dashboard.php');
        exit();
    }

    $noteId = intval($_POST['note_id']); // Sanitize the incoming note ID
    
    // Retrieve the filename associated with the note (for file deletion)
    $getFilename = safe_prepare($dbConnection, 'SELECT Filename FROM notes WHERE note_id = ? AND user_id = ?', 'dashboard.php');
    $getFilename->bind_param('is', $noteId, $userId);
    safe_execute($getFilename, 'dashboard.php');

    $filenameResult = $getFilename->get_result();
    $noteData = $filenameResult->fetch_assoc();
    $getFilename->close();

    if($noteData) {
        $filePath = __DIR__ . '/../../../uploads/' . $noteData['Filename'];
        
        // Delete the file from the server if it exists
        if(file_exists($filePath)) {
           unlink($filePath);
        }
        
        // Delete the note record from the database
        $deleteNote = safe_prepare($dbConnection, 'DELETE FROM notes WHERE note_id = ? AND user_id = ?', 'dashboard.php');
        $deleteNote->bind_param('is', $noteId, $userId);
        safe_execute($deleteNote, 'dashboard.php');
    }
    $deleteNote->close();

    // Redirect back to the dashboard after deletion
    header('Location:../dashboard.php');
    exit();
} else {
    header('Location:../dashboard.php');
    exit();
}
?>