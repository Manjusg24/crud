<?php
session_start();

include "../includes/db.php";

$userId = $_SESSION['userid'];

if(isset($_GET['delete'])) {
    $noteId = intval($_GET['delete']); // Sanitize the incoming note ID
    
    // Retrieve the filename associated with the note (for file deletion)
    $getFilename = $conn->prepare("SELECT Filename FROM notes WHERE note_id = ? AND user_id = ?");
    $getFilename->bind_param('is',$noteId,$userId);
    $getFilename->execute();

    $filenameResult = $getFilename->get_result();
    $noteData = $filenameResult->fetch_assoc();

    if($noteData) {
        $filePath = "../uploads/" . $noteData['Filename'];
        
        // Delete the file from the server if it exists
        if(file_exists($filePath)) {
           unlink($filePath);
        }
        
        // Delete the note record from the database
        $deleteNote = $conn->prepare("DELETE FROM notes WHERE note_id = ? AND user_id = ?");
        $deleteNote->bind_param('is',$noteId,$userId);
        $deleteNote->execute();
    }

    // Redirect back to the dashboard after deletion
    header("location:../dashboard.php");
    exit();
} else {
    echo "Invalid Request";
}
?>