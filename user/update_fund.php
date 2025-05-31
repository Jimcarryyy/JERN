<?php
include '../config/db_connection.php';

$id = $_POST['id'];
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$dateIssued = $_POST['dateIssued'];
$amount = $_POST['amount'];

$sql = "UPDATE funds SET firstName = '$firstName', lastName = '$lastName', dateIssued = '$dateIssued', amount = '$amount' WHERE id = $id";
if ($conn->query($sql) === TRUE) {
    echo "Fund updated successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>