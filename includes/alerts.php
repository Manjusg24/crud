<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['auth_error'])) {
    echo "<div class='error-message'>" . htmlspecialchars($_SESSION['auth_error']) . "</div>";
    unset($_SESSION['auth_error']);
}

if (isset($_SESSION['success'])) {
    echo "<div class='success-message'>" . htmlspecialchars($_SESSION['success']) . "</div>";
    unset($_SESSION['success']);
}

?>