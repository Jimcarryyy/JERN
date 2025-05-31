<?php
// Start the session
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to the loading page
header("Location: ../util/loading-page.html?redirect=../auth/login.php");
exit();
?>
