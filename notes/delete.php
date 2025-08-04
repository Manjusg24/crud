<?php
session_start();

include "../includes/db.php";

if(isset($_GET['delete'])) {
    $del = intval($_GET['delete']);
    $sql = $conn->prepare("DELETE from notes where note_id = ? ");
    $sql->bind_param('i',$del);

    if(!$sql->execute()) {
        echo "Error deleting note";
    } else {
        header("location:../dashboard.php");
        exit();
    }
} else {
    echo "Invalid Request";
}
?>