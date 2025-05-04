<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$search_query = "";
$customers = [];

if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
    $customers = $conn->query("SELECT * FROM customers WHERE id LIKE '%$search_query%' OR name LIKE '%$search_query%' OR phone LIKE '%$search_query%' OR email LIKE '%$search_query%'");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Customers</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Search Customers</h2>
            <a href="customer_list.php" class="btn btn-secondary">Back to Customers</a>
        </div>

        <!-- Search Form -->
        <form method="GET" action="search_customer.php" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by Name, Phone, or Email" value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="btn btn-success">Search</button>
                <a href="search_customer.php" class="btn btn-danger">Reset</a>
            </div>
        </form>

        <div class="card p-4 shadow-lg">
            <h4 class="mb-3">Search Results</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($customers && $customers->num_rows > 0): ?>
                        <?php while ($row = $customers->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['address']; ?></td>
                                <td><?php echo $row['phone']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td>
                                    <a href="edit_customer.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="customer_list.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No customers found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
