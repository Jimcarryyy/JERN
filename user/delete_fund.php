<?php
include '../config/db_connection.php';

$id = $_POST['id'];
$sql = "DELETE FROM funds WHERE id = $id";
if ($conn->query($sql) === TRUE) {
    echo "Fund deleted successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>