<?php 

$host = "sql109.infinityfree.com";
$user = "if0_38223622";
$pass = "plUE4RuFUF";
$db = "if0_38223622_jern";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>