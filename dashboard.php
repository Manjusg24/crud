<?php
session_start();

include "includes/db.php";

// Prevent browser from caching this page (except for bfcache, which requires JS to handle)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

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
<h1>Notes Dashboard</h1>
    <h3><a href="../auth/logout.php">Click to logout</a></h3>
    <h2>Add New Note</h2>
    <form action="dashboard.php" method="POST">
        <label for="title">Title:</label><br>
        <input type="text" name="title" id="title"><br><br>
        <label for="description">Description:</label><br>
        <textarea name="description" id="description" rows="4"></textarea><br><br>
        <button>Add Note</button><br><br>
    </form>
    <?php
    if($_SERVER['REQUEST_METHOD'] == "POST") {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);

        if(empty($title) || empty($description)) {
            echo "Both fields are required";
        } else {
            $insertQuery = $conn->prepare("insert into `notes`(`Title`,`Description`) values(?, ?)");
            $insertQuery->bind_param('ss',$title,$description);
            $insertQuery->execute();
            $insertQuery->close();    
        }

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
            $note =mysqli_query($conn,"select * from notes");
            $sno = 1;
            while($noteResult = mysqli_fetch_assoc($note)) {
                echo "<tr>";
                echo "<td>" . $sno++ . "</td>";
                echo "<td>" . htmlspecialchars($noteResult['Title']) . "</td>";
                echo "<td>" . htmlspecialchars($noteResult['Description']) . "</td>";
                echo "<td class='action-buttons'>";
                echo "<a href='notes/edit.php' class='edit'>Edit</a>";
                echo "<a href='notes/delete.php' class='delete'>Delete</a>";
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
