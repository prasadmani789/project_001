<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Validate the ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Quotation ID.");
}

$id = intval($_GET['id']);

// Delete the quotation
$deleteQuery = $conn->prepare("DELETE FROM quotations WHERE id = ?");
$deleteQuery->bind_param("i", $id);

if ($deleteQuery->execute()) {
    header("Location: quotations.php");
    exit();
} else {
    echo "Error deleting quotation.";
}
?>
