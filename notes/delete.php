<?php
session_start();

include "../includes/db.php";

if(isset($_GET['delete'])) {
    $noteId = intval($_GET['delete']);
    $deleteNote = $conn->prepare("DELETE from notes where note_id = ? ");
    $deleteNote->bind_param('i',$noteId);

    if(!$deleteNote->execute()) {
        echo "Error deleting note";
    } else {
        header("location:../dashboard.php");
        exit();
    }
} else {
    echo "Invalid Request";
}
?>