<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Quotation Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background: url('cmp-1.png') no-repeat center center fixed;
            background-size: cover;
        }
        .dashboard-container {
            background: rgba(255, 255, 255, 0.8); /* Light overlay for readability */
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            max-width: 600px;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Quotation System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="customers.php">Customers</a></li>
                    <li class="nav-item"><a class="nav-link" href="quotations.php">Quotations</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-danger text-white" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="dashboard-container text-center">
        <h2>Quotation System Management</h2>
        <p class="lead"></p>
        <div class="d-flex justify-content-center gap-3 mt-4">
            <a href="products.php" class="btn btn-primary btn-lg">Product Management</a>
            <a href="customers.php" class="btn btn-success btn-lg">Customer Management</a>
            <a href="quotations.php" class="btn btn-warning btn-lg">Quotation Management</a>
        </div>
    </div>
</body>
</html>
