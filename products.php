<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Handle Add Product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    // Input validation
    $product_id = trim($_POST['product_id']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $gst_percent = floatval($_POST['gst_percent']);
    $price = floatval($_POST['price']);

    // Check for empty fields
    if (empty($product_id) || empty($name)) {
        $error = "Please fill all required fields!";
    } else {
        // Prepared statement with correct columns
        $stmt = $conn->prepare("INSERT INTO products (id, name, description, gst_percent, price) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdd", $product_id, $name, $description, $gst_percent, $price);
        
        if ($stmt->execute()) {
            $success = "Product added successfully!";
            // Clear form
            $_POST = array();
        } else {
            $error = "Error adding product: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!-- Rest of your HTML remains the same -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Quotation System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="customers.php">Customers</a></li>
                    <li class="nav-item"><a class="nav-link" href="quotations.php">Quotations</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-danger text-white" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Manage Products</h2>
            <div>
                <a href="view_products.php" class="btn btn-success">View Products</a>
                <a href="dashboard.php" class="btn btn-secondary">Back</a>
            </div>
        </div>

        <div class="card p-4 shadow-lg">
            <h4 class="mb-3">Add New Product</h4>
            <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Product ID</label>
                    <input type="text" name="product_id" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">GST (%)</label>
                    <input type="number" step="0.01" name="gst_percent" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>
                <button type="submit" name="add_product" class="btn btn-primary w-100">Add Product</button>
            </form>
        </div>
    </div>
</body>
</html>