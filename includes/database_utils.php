<?php
// Start session only if it's not already active (prevents duplicate session_start() errors)
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

function redirect_on_error(string $redirectUrl) {
    $_SESSION['error'] = "Something went wrong. Please try again later.";
    header("Location:" . $redirectUrl);
    exit();
}

function safe_prepare(mysqli $dbConnection, string $sqlQuery, string $redirectUrl) {
    $preparedStatement = $dbConnection->prepare($sqlQuery);

    if(!$preparedStatement) {
        // Log the error to a secure place (don't show to users)
        error_log("Prepare failed: " . $dbConnection->error);
        redirect_on_error($redirectUrl);
    }
    return $preparedStatement;
}

function safe_execute(mysqli_stmt $statement, string $redirectUrl) {

    if(!$statement->execute()) {
        // Log the error to a secure place (don't show to users)
        error_log("Execute failed: " . $statement->error);
        redirect_on_error($redirectUrl);
    }
}

?>