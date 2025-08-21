<?php
session_start();

// Include database connection and utility functions
require_once "../includes/db.php";
require_once "../includes/database_utils.php";

// Redirect to homepage if user is not logged in
if(!isset($_SESSION['userid'])) {
    header("location: ../index.php");
    exit();
}

// Get the note ID from the query parameter and ensure it is an integer
$noteId = intval($_GET['download']);
$userId = $_SESSION['userid'];

// Validate note ID; if invalid, redirect to dashboard
if($noteId <= 0) {
    header("location: ../dashboard.php");
    exit();
}

// Prepare and execute a secure query to fetch file information for the note
$file = safe_prepare($dbConnection, "SELECT OriginalFilename, Filename FROM notes WHERE note_id = ? AND user_id = ?", "dashboard.php");
$file->bind_param("is", $noteId, $userId);
safe_execute($file, "dashboard.php");
$res = $file->get_result();

// If no matching note is found for this user, redirect to dashboard
if($res->num_rows == 0) {
    header("location: ../dashboard.php");
    exit();
}

// Retrieve file details from database result
$path = $res->fetch_assoc();

// Build the absolute path to the file on the server
$serverFilePath = realpath(__DIR__ . "/../../../uploads/" . $path['Filename']);

// Check if the file exists on the server; if not, redirect to dashboard
if(!file_exists($serverFilePath)) {
    header("location: ../dashboard.php");
    exit();
}

// Sanitize the original filename to prevent header injection issues
$originalFileName = basename($path['OriginalFilename']);
$originalFileName = str_replace(["\r", "\n"], '', $originalFileName);

// Set appropriate headers to prompt file download in browser
header('Content-Description: File Transfer');   // Describe response as file download
header('Content-Type: application/octet-stream');   // Set MIME type for binary file
header('Content-Disposition: attachment; filename="' . $originalFileName . '"');    // Force download with original filename
header('Expires: 0');                               // Disable caching
header('Cache-Control: must-revalidate');           // Ensure cache is validated before use
header('Pragma: public');                           // Support HTTP/1.0 cache control
header('Content-Length: ' . filesize($serverFilePath)); // Specify file size for download

// Output the file content to the response
readfile($serverFilePath);
exit();
?>