<?php
session_start();

require_once "../includes/db.php";
require_once "../includes/database_utils.php";
require_once "../includes/alerts.php";

if(!isset($_SESSION['userid'])) {
    header('Location:../index.php');
    exit();
}

$userId = $_SESSION['userid'];

if(isset($_GET['view'])) {
    $noteId = intval($_GET['view']);

    $getNoteFile = safe_prepare($dbConnection, 'SELECT Filename, OriginalFilename FROM notes WHERE note_id = ? AND user_id = ?', 'dashboard.php');
    $getNoteFile->bind_param("is", $noteId, $userId);
    safe_execute($getNoteFile, 'dashboard.php');

    $result = $getNoteFile->get_result();
    $noteFileData = $result->fetch_assoc();
    
    if(!$noteFileData) {
        $_SESSION['error'] = "Note not found!";
        header('Location: ../dashboard.php');
        exit();
    }

    $getNoteFile->close();

    $filePath = __DIR__ . '/../../../uploads/' . $noteFileData['Filename'];
    $filename = htmlspecialchars($noteFileData['OriginalFilename']);

    if($filename) {

        if(file_exists($filePath)) {
            $extension = strtolower(pathinfo($filePath,PATHINFO_EXTENSION));

            switch($extension) {
                case 'txt':
                    header('Content-Type:text/plain');
                    break;

                case 'jpg':
                case 'jpeg':
                    header('Content-Type:image/jpeg');
                    break;
                
                case 'pdf':
                    header('Content-Type:application/pdf');
                    header('Content-Disposition:inline; filename="' . $filename . '"');
                    break;
                
                default: $_SESSION['error'] = "Unsupported file type";
                        header('Location: ../dashboard.php');
                        exit();

            }
            readfile($filePath);
            exit();
        } else {
            $_SESSION['error'] = "File not found on server";
            header('Location: ../dashboard.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "Invalid file";
        header('Location: ../dashboard.php');
        exit();
    }
} else {
    header('Location: ../dashboard.php');
    exit();
}    
    


?>