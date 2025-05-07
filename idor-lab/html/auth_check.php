<?php
// html/auth_check.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    // Not logged in, redirect to login page
    header('Location: login.php');
    exit;
}

// User is logged in, proceed with loading the page.
// We can also set some commonly used session variables here for convenience in other scripts
$loggedInUserId = $_SESSION['user_id'];
$loggedInUsername = $_SESSION['username'];
$loggedInFullName = $_SESSION['full_name'];
$loggedInUserAlbumId = $_SESSION['album_id'];
?>
