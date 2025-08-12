<?php
session_start();

include "../includes/db.php";

if(isset($_GET['view'])) {
    $noteId = intval($_GET['view']);

    $getNoteFile = $conn->prepare("SELECT Filename, OriginalFilename FROM notes where note_id = ?");
    $getNoteFile->bind_param("i",$noteId);
    $getNoteFile->execute();
    $result = $getNoteFile->get_result();
    $noteFileData = $result->fetch_assoc();

    $getNoteFile->close();

    $filePath = "../uploads/" . $noteFileData['Filename'];
    $filename = htmlspecialchars($noteFileData['OriginalFilename']);

    if($filename) {

        if(file_exists($filePath)) {
            $extension = strtolower(pathinfo($filePath,PATHINFO_EXTENSION));

            switch($extension) {
                case 'txt':
                    header("content-type:text/plain");
                    break;

                case 'jpg':
                case 'jpeg':
                    header("content-type:image/jpeg");
                    break;
                
                case 'pdf':
                    header("content-type:application/pdf");
                    header('Content-Disposition:inline; filename="' . $filename . '"');
                    break;
                
                default: echo "File not found!";

            }
            readfile($filePath);
        }   
    }
}

?>