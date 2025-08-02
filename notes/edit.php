<?php
include "../includes/db.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    if(isset($_GET['edit'])) {
        
        $edit = intval($_GET['edit']);  // sanitize user input

        // use prepared statement
        $sql = $conn->prepare("select * from notes where note_id=?");
        $sql->bind_param("i",$edit);
        $sql->execute();
        $res = $sql->get_result();

        $note = mysqli_fetch_assoc($res);  // safely fetch result
    
        if($note) {
            echo  "<form action='dashboard.php' method='POST'>
                    <label for='title'>Title:</label><br>
                    <input type='text' name='title' id='title' value='" . htmlspecialchars($note['Title']) . "'><br><br>
                    <label for='description'>Description:</label><br>
                    <textarea name='description' id='description' rows='4'>" . htmlspecialchars($note['Description']) . "</textarea><br><br>
                    <button>Update Note</button><br><br>
                    </form>";
        } else {
            echo "Note not found!";
        }
      
    }
    ?>
</body>
</html>