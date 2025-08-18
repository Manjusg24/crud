<?php
// Start session only if it's not already active (prevents duplicate session_start() errors)
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

function regenerateCsrfToken() {
    // Generate a new CSRF token and store creation time
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_time'] = time();
}

function ensureFreshCsrfToken() {
    $timeout = 900;// 15 minutes

    // If token is missing or expired, regenerate a new one
    if(!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time']) ||(time() - $_SESSION['csrf_token_time'] > $timeout)) {
        regenerateCsrfToken();
    }

}
?>