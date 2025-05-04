<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: quotations.php");
    exit();
}

$quotation_id = intval($_GET['id']);

// Fetch Quotation Details
$quotation_query = $conn->query("SELECT q.id, q.date_requested, c.name AS customer_name, c.address, c.phone, c.email FROM quotations q JOIN customers c ON q.customer_id = c.id WHERE q.id = $quotation_id");
$quotation = $quotation_query->fetch_assoc();

if (!$quotation) {
    header("Location: quotations.php");
    exit();
}

// Fetch Quotation Items
$quotation_items_query = $conn->query("SELECT p.id AS product_id, p.name AS product_name, p.description, p.price, qi.quantity FROM quotation_items qi JOIN products p ON qi.product_id = p.id WHERE qi.quotation_id = $quotation_id");
$quotation_items = [];
while ($row = $quotation_items_query->fetch_assoc()) {
    $quotation_items[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Quotation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow-lg p-4">
            <h2 class="text-center">Quotation Details</h2>
            <p><strong>Customer:</strong> <?php echo htmlspecialchars($quotation['customer_name']); ?></p>
            <p><strong>Date Requested:</strong> <?php echo htmlspecialchars($quotation['date_requested']); ?></p>
            <hr>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_cost = 0;
                    foreach ($quotation_items as $item) {
                        $total_price = $item['price'] * $item['quantity'];
                        $total_cost += $total_price;
                        echo "<tr>
                            <td>{$item['product_id']}</td>
                            <td>{$item['product_name']}</td>
                            <td>{$item['description']}</td>
                            <td>₹" . number_format($item['price'], 2) . "</td>
                            <td>{$item['quantity']}</td>
                            <td>₹" . number_format($total_price, 2) . "</td>
                        </tr>";
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-end">Total Amount:</th>
                        <th>₹<?php echo number_format($total_cost, 2); ?></th>
                    </tr>
                </tfoot>
            </table>
            <div class="text-center">
                <a href="print_quotation.php?id=<?php echo $quotation_id; ?>" target="_blank" class="btn btn-primary">Print Quotation</a>
                <a href="quotations.php" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</body>
</html>
