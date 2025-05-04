<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$error = "";
$success = "";

// Check if the product ID is set
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: view_products.php");
    exit();
}

$product_id = $_GET['id'];

// Fetch the existing product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("s", $product_id); // Changed to "s" if ID is string
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    header("Location: view_products.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['update_product'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $gst_percent = floatval($_POST['gst_percent']);
    $price = floatval($_POST['price']);

    // Validate input
    if (empty($name) || empty($description)) {
        $error = "Name and Description are required!";
    } elseif ($gst_percent < 0 || $gst_percent > 100) {
        $error = "GST must be between 0 and 100!";
    } elseif ($price <= 0) {
        $error = "Price must be greater than 0!";
    } else {
        // Use prepared statement to update product details
        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, gst_percent = ?, price = ? WHERE id = ?");
        $stmt->bind_param("ssdds", $name, $description, $gst_percent, $price, $product_id);

        if ($stmt->execute()) {
            $success = "Product updated successfully!";
            // Refresh product data
            $product['name'] = $name;
            $product['description'] = $description;
            $product['gst_percent'] = $gst_percent;
            $product['price'] = $price;
        } else {
            $error = "Error updating product: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => alert.remove());
        }, 5000);
    </script>
</head>
<body class="bg-light">
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Quotation System</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="customers.php">Customers</a></li>
                    <li class="nav-item"><a class="nav-link" href="quotations.php">Quotations</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-danger text-white" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Edit Product Form -->
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Edit Product</h2>
            <a href="view_products.php" class="btn btn-secondary">Back to Products</a>
        </div>
        
        <div class="card p-4 shadow-lg">
            <h4 class="mb-3">Update Product Details</h4>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Product ID</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($product['id']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Product Name*</label>
                    <input type="text" name="name" class="form-control" 
                           value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description*</label>
                    <textarea name="description" class="form-control" rows="3" required><?php 
                        echo htmlspecialchars($product['description']); 
                    ?></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">GST %*</label>
                        <input type="number" step="0.01" name="gst_percent" class="form-control" 
                               value="<?php echo htmlspecialchars($product['gst_percent']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Price*</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" name="price" class="form-control" 
                                   value="<?php echo htmlspecialchars($product['price']); ?>" required>
                        </div>
                    </div>
                </div>
                <button type="submit" name="update_product" class="btn btn-primary w-100 py-2">
                    Update Product
                </button>
            </form>
        </div>
    </div>
</body>
</html>