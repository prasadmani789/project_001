<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$search_query = "";
$products = [];

if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
    $products = $conn->query("SELECT * FROM products WHERE id LIKE '%$search_query%' OR name LIKE '%$search_query%'");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Products</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Search Products</h2>
            <a href="view_products.php" class="btn btn-secondary">Back to Products</a>
        </div>

        <!-- Search Form -->
        <form method="GET" action="search_products.php" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by Product ID or Name" value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="btn btn-success">Search</button>
                <a href="search_products.php" class="btn btn-danger">Reset</a>
            </div>
        </form>

        <div class="card p-4 shadow-lg">
            <h4 class="mb-3">Search Results</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>HSN Code</th>
                        <th>Height</th>
                        <th>GST (%)</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($products && $products->num_rows > 0): ?>
                        <?php while ($row = $products->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['description']; ?></td>
                                <td><?php echo $row['hsn_code']; ?></td>
                                <td><?php echo $row['height']; ?></td>
                                <td><?php echo $row['gst_percent']; ?>%</td>
                                <td><?php echo $row['price']; ?></td>
                                <td>
                                    <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="delete_product.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No products found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
