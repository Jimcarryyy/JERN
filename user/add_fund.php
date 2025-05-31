<?php
include '../config/db_connection.php';

$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$dateIssued = $_POST['dateIssued'];
$amount = $_POST['amount'];

$sql = "INSERT INTO funds (firstName, lastName, dateIssued, amount) VALUES ('$firstName', '$lastName', '$dateIssued', '$amount')";
if ($conn->query($sql) === TRUE) {
    echo "Fund added successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>