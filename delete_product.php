<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("s", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Product deleted successfully!";
        $_SESSION['message_type'] = "success"; // Bootstrap success alert
    } else {
        $_SESSION['message'] = "Failed to delete product.";
        $_SESSION['message_type'] = "danger"; // Bootstrap danger alert
    }

    $stmt->close();
    $conn->close();
}

header("Location: view_products.php");
exit();
