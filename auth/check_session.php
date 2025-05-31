<?php
session_start();

$sessionExpiry = $_SESSION['session_expiry'];

// Check if session is expired
$expired = isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $sessionExpiry;

// Return JSON response
header('Content-Type: application/json');
echo json_encode(['expired' => $expired]);
?>
