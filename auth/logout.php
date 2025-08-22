<?php
session_start();

require_once '../includes/csrf.php';

ensureFreshCsrfToken(); // Make sure token exists and is fresh

// Prevent browser from caching this page
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

// Check request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location:../dashboard.php');
    exit();
}

// Check CSRF token is present and valid
if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location:../dashboard.php');
    exit();
}

// Proceed with logout
session_unset();
session_destroy();


// Redirect after logout
header('Location:../index.php');
exit();
?>
