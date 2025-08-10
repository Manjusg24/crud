<?php
session_start();

include "includes/db.php";

// Prevent browser from caching this page (except for bfcache, which requires JS to handle)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Redirect unauthenticated users
if (!isset($_SESSION['username'])) {
    header("location:index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Notes Dashboard</title>
    <link rel="icon" href="assets/images/favicon.png">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <nav>
        <h1>Notes Dashboard</h1>
        <h3><a href="auth/logout.php">Click to logout</a></h3>
    </nav>
    <h2>Add New Note</h2>
    
    <?php
        // Show one-time error message
        include "includes/alerts.php";
    ?>

    <!-- Form for creating a new note -->
    <form action="dashboard.php" method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label><br>
        <input type="text" name="title" id="title"><br><br>
        <label for="description">Description:</label><br>
        <textarea name="description" id="description" rows="4"></textarea><br><br>
        <label for="file">Upload File:</label>
        <input type="file" name="file" id="file"><br><br>
        <button>Add Note</button><br><br>
    </form>
    
    <?php
    // Handle form submission on POST request
    if($_SERVER['REQUEST_METHOD'] == "POST") {
        $noteTitle = trim($_POST['title']);
        $noteDescription = trim($_POST['description']);
        $originalFilename = "";
        $targetFilePath = "";
        $fileIsValid = false;
        $error = [];

        // Validate required input fields
        if(empty($noteTitle) || empty($noteDescription)) {
            $error[] = "Both fields are required.";
        }

        // Handle file upload if a file is provided
        if(isset($_FILES['file']) && $_FILES['file']['error'] !== 4) {
            $uploadDirectory = "uploads/";
            $originalFilename = preg_replace("/[^a-zA-Z0-9\.\-_]/","",basename($_FILES['file']['name']));
            $targetFilePath = $uploadDirectory . $originalFilename;

            // Use finfo to detect MIME type and ensure it's allowed
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo,$_FILES['file']['tmp_name']);
            $allowedMimeTypes = ['application/pdf', 'image/jpeg', 'text/plain'];

            // Checks file size not exceed 5MB
            $maxFileSize = 5 * 1024 *1024;

            if($_FILES['file']['size'] > $maxFileSize) {
                $error[] = "File size exceeds 5MB";
            } else {

                // Validate file type
                if(!in_array($mime,$allowedMimeTypes)) {
                    $error[] = "Invalid file type";
                } else {
                    $fileIsValid = true;
                }
            }
        }

        // If no errors, insert note into the database
        if(empty($error)) {
            $insertQuery = $conn->prepare("insert into `notes`(`Title`,`Description`,`Filename`) values(?, ?, ?)");
            $insertQuery->bind_param('sss',$noteTitle,$noteDescription,$originalFilename);
                
            if($insertQuery->execute()) {
                // Move uploaded file to target directory if valid
                if($fileIsValid) {
                    move_uploaded_file($_FILES['file']['tmp_name'], $targetFilePath);
                }
                $_SESSION['success'] = "Note saved successfully";
            } else {
                $_SESSION['error'] = "Failed to save note";
            }
            $insertQuery->close();
        } else {
            // Store validation errors in session
            $_SESSION['error'] = implode("<br>",$error);
        }
        // Prevent resubmission on refresh
        header("location:dashboard.php");
        exit();
    }
    ?>
    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>Title</th>
            <th>Description</th>
            <th>File Name</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
            <?php
            // Fetch and display all notes
            $notesResult = mysqli_query($conn,"select * from notes");
            $serialNumber = 1;
            while($note = mysqli_fetch_assoc($notesResult)) {
                echo "<tr>";
                echo "<td>" . $serialNumber++ . "</td>";
                echo "<td>" . htmlspecialchars($note['Title']) . "</td>";
                echo "<td>" . htmlspecialchars($note['Description']) . "</td>";
                $targetFilePath = "uploads/" . $note['Filename'];
                if(!empty($note['Filename']) && file_exists($targetFilePath)) {
                    echo "<td>" . htmlspecialchars($note['Filename']) . " <a href='uploads/" . urlencode($note['Filename']) . "' class='download-link' download>Download</a> </td>";
                } else {
                    echo "<td>---</td>";
                }
                echo "<td class='action-buttons'>";
                echo "<a href='notes/edit.php?edit=" . $note['note_id'] . "' class='edit'>Edit</a>";
                echo "<a href='notes/delete.php?delete=" . $note['note_id'] . "' class='delete' onclick='return confirm(\"Are you sure. Do you want to delete this note?\");'>Delete</a>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
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