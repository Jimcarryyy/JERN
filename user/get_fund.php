<?php
include '../config/db_connection.php';

$id = $_GET['id'];
$sql = "SELECT * FROM funds WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $fund = $result->fetch_assoc();
    echo json_encode($fund);
} else {
    echo "Fund not found";
}

$conn->close();
?>