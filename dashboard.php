<?php
session_start();

include "includes/db.php";

// Prevent browser from caching this page (except for bfcache, which requires JS to handle)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Redirect unauthenticated users
if (!isset($_SESSION['username']) || !isset($_SESSION['userid'])) {
    header("location:index.php");
    exit();
}

if(!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$userId = $_SESSION['userid'];
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
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <label for="title">Title:</label><br>
        <input type="text" name="title" id="title"><br><br>
        <label for="description">Description:</label><br>
        <textarea name="description" id="description" rows="4"></textarea><br><br>
        <label for="file">Upload File:</label>
        <input type="file" name="file" id="file"><br><br>
        <button>Add Note</button><br><br>
    </form>
    
    <?php
    // Generate a unique file name to avoid overwriting existing files
    function generateUniqueFileName($sanitizedFilename) {
        $timestamp = date('Ymd_His');
        $randomString = bin2hex(random_bytes(4));
        $fileExtension = pathinfo($sanitizedFilename,PATHINFO_EXTENSION);
        $baseName = pathinfo($sanitizedFilename,PATHINFO_FILENAME);
        return $baseName . "_" . $timestamp . "_" . $randomString . "." . $fileExtension;
    }
    
    // Handle form submission on POST request
    if($_SERVER['REQUEST_METHOD'] == "POST") {
        
        if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("CSRF token not set!");
        }

        $noteTitle = trim($_POST['title']);
        $noteDescription = trim($_POST['description']);
        $originalFilename = "";
        $sanitizedFilename = "";
        $uniqueFilename = "";
        $targetFilePath = "";
        $displayFilePath = "";
        $fileIsValid = false;
        $error = [];

        // Validate required input fields
        if(empty($noteTitle) || empty($noteDescription)) {
            $error[] = "Both fields are required.";
        }

        // Handle file upload if a file is provided
        if(isset($_FILES['file']) && $_FILES['file']['error'] !== 4) {
            $uploadDirectory = __DIR__ . "/../../uploads/";
            $originalFilename = basename($_FILES['file']['name']);
            $sanitizedFilename = preg_replace("/[^a-zA-Z0-9\.\-_]/","",basename($_FILES['file']['name']));
            $uniqueFilename = generateUniqueFileName($sanitizedFilename);
            $targetFilePath = $uploadDirectory . $uniqueFilename;

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
            $insertQuery = $conn->prepare("insert into `notes`(`Title`,`Description`,`OriginalFilename`,`Filename`,`user_id`) values(?, ?, ?, ?, ?)");
            $insertQuery->bind_param('sssss',$noteTitle,$noteDescription,$originalFilename,$uniqueFilename,$userId);
                
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
            // How many notes to display per page
            $notesPerPage = 5;

            // Get the current page from the URL (default to 1 if not set or invalid)
            $currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;

            // Calculate the OFFSET for SQL query (skip this many records)
            $offset = ($currentPage - 1) * $notesPerPage;

            $totalNotesQuery = mysqli_query($conn,"SELECT count(*) AS total FROM notes WHERE user_id = '$userId'");

            $totalNotesRow = mysqli_fetch_assoc($totalNotesQuery);

            $totalNotes = $totalNotesRow['total'];

            $totalPages = ceil($totalNotes/$notesPerPage);

            // Fetch and display all notes
            $notesResult = mysqli_query($conn,"SELECT * FROM notes WHERE user_id = '$userId' limit $notesPerPage offset $offset");
            $serialNumber = $offset + 1;
            while($note = mysqli_fetch_assoc($notesResult)) {
                echo "<tr>";
                echo "<td>" . $serialNumber++ . "</td>";
                echo "<td>" . htmlspecialchars($note['Title']) . "</td>";
                echo "<td>" . htmlspecialchars($note['Description']) . "</td>";
                $displayFilePath = __DIR__ . "/../../uploads/" . $note['Filename'];
                if(!empty($note['Filename']) && file_exists($displayFilePath)) {
                    echo "<td><div class='file-cell'><span class='filename'> <a href='notes/view.php?view=" . $note['note_id'] . "' target='_blank' rel='noopener noreferrer'>" . htmlspecialchars($note['OriginalFilename']) . "</a></span> <a href='uploads/" . rawurlencode($note['Filename']) . "' class='download-link' download='" . htmlspecialchars($note['OriginalFilename']) . "'>Download</a> </div> </td>";
                } else {
                    echo "<td>---</td>";
                }
                echo "<td class='action-buttons'>";
                echo "<a href='notes/edit.php?edit=" . $note['note_id'] . "'>Edit</a>";
                echo "<form action='notes/delete.php' method='POST' onclick='return confirm(\"Are you sure. Do you want to delete this note?\");'>
                    <input type='hidden' name='note_id' value='" . $note['note_id'] . "'>
                    <input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>
                    <button type='submit' class='delete'>Delete</button>
                     </form>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    <?php
        // Display pagination navigation:
        echo "<div class='pagination'>";
        
        // Shows "prev" link if not on the first page
        if($currentPage > 1) {
            echo "<a href='?page=" . ($currentPage - 1) . "'>&laquo; prev</a>";
        }

        // Loops through all pages to generate page number links, highlighting the current page
        if($totalPages > 1) {
            for($i = 1; $i <= $totalPages; $i++) {
                $class = ($i == $currentPage) ? 'active' : '';
                echo "<a href='?page={$i}' class='{$class}'>{$i}</a>";
            }
        }
        
        // Shows "next" link if not on the last page
        if($currentPage < $totalPages) {
            echo "<a href='?page=" . ($currentPage + 1) . "'>next &raquo;</a>";
        }

        echo "</div>";
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