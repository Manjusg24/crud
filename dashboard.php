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
    // Show one-time error message if fields were empty
    if(isset($_SESSION['error'])) {
        echo htmlspecialchars($_SESSION['error']);
        unset($_SESSION['error']);
    }
    ?>
    
    <!-- Form for creating a new note -->
    <form action="dashboard.php" method="POST">
        <label for="title">Title:</label><br>
        <input type="text" name="title" id="title"><br><br>
        <label for="description">Description:</label><br>
        <textarea name="description" id="description" rows="4"></textarea><br><br>
        <button>Add Note</button><br><br>
    </form>

    <?php
    // Handle form submission
    if($_SERVER['REQUEST_METHOD'] == "POST") {
        $noteTitle = trim($_POST['title']);
        $noteDescription = trim($_POST['description']);

        if(empty($noteTitle) || empty($noteDescription)) {
            $_SESSION['error'] = "Both fields are required.";
        } else {

            // Insert the new note into the database
            $insertQuery = $conn->prepare("insert into `notes`(`Title`,`Description`) values(?, ?)");
            $insertQuery->bind_param('ss',$noteTitle,$noteDescription);
            $insertQuery->execute();
            $insertQuery->close();    
        }

        // Prevent resubmission on refresh
        header("location:dashboard.php");
        exit();
    }
    ?>
    <table>
        <thead>
        <tr>
            <th>Sno</th>
            <th>Title</th>
            <th>Description</th>
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