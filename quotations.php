<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Fetch Quotations
$quotations = $conn->query("
    SELECT q.id, c.name AS customer_name, q.date_requested 
    FROM quotations q
    JOIN customers c ON q.customer_id = c.id
    ORDER BY q.date_requested DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Quotations</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Quotation System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="customers.php">Customers</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-danger text-white" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Quotation Management Section -->
    <div class="container mt-5">
        <h2 class="text-center">Manage Quotations</h2>

        <!-- Buttons moved to the top -->
        <div class="d-flex justify-content-between mb-3">
            <a href="dashboard.php" class="btn btn-secondary">Back</a>
            <a href="add_quotation.php" class="btn btn-primary">Add New Quotation</a>
        </div>

        <!-- Quotation List -->
        <div class="card p-4 shadow-lg">
            <h4 class="mb-3">Quotation List</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Quotation ID</th>
                        <th>Customer</th>
                        <th>Date Requested</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $quotations->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['customer_name']; ?></td>
                            <td><?php echo $row['date_requested']; ?></td>
                            <td>
                                <a href="view_quotation.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">View</a>
                                <a href="edit_quotation.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_quotation.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
