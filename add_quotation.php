<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Fetch Products and Customers for Dropdowns
$products = $conn->query("SELECT * FROM products ORDER BY id ASC");
$customers = $conn->query("SELECT * FROM customers ORDER BY id ASC");

// Handle Add Quotation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_quotation'])) {
    $customer_id = $_POST['customer_id'];
    $date_requested = $_POST['date_requested'];
    $selected_products = $_POST['product_id'];
    $quantities = $_POST['quantity'];

    if (!empty($selected_products)) {
        $quotation_query = "INSERT INTO quotations (customer_id, date_requested) VALUES ('$customer_id', '$date_requested')";
        if ($conn->query($quotation_query)) {
            $quotation_id = $conn->insert_id;

            foreach ($selected_products as $index => $product_id) {
                $quantity = $quantities[$index];
                $conn->query("INSERT INTO quotation_items (quotation_id, product_id, quantity) VALUES ('$quotation_id', '$product_id', '$quantity')");
            }

            $success = "Quotation added successfully!";
        } else {
            $error = "Error creating quotation!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Quotation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center">Add New Quotation</h2>

        <div class="card p-4 shadow-lg mt-4">
            <h4 class="mb-3">Quotation Details</h4>
            <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Select Customer</label>
                    <select name="customer_id" class="form-control" required>
                        <option value="">Choose...</option>
                        <?php while ($customer = $customers->fetch_assoc()): ?>
                            <option value="<?php echo $customer['id']; ?>"><?php echo $customer['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Date of Request</label>
                    <input type="date" name="date_requested" class="form-control" required>
                </div>

                <h5 class="mt-3">Select Products</h5>
                <div id="product-list">
                    <div class="row product-item">
                        <div class="col-md-6">
                            <select name="product_id[]" class="form-control" required>
                                <option value="">Choose Product...</option>
                                <?php while ($product = $products->fetch_assoc()): ?>
                                    <option value="<?php echo $product['id']; ?>">
                                        <?php echo $product['name'] . " (ID: " . $product['id'] . ")"; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="quantity[]" class="form-control" placeholder="Quantity" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-danger remove-product">Remove</button>
                        </div>
                    </div>
                </div>
                <button type="button" id="add-product" class="btn btn-secondary mt-3">+ Add More Products</button>

                <button type="submit" name="add_quotation" class="btn btn-primary w-100 mt-3">Create Quotation</button>
            </form>
        </div>

        <div class="mt-3 text-center">
            <a href="quotations.php" class="btn btn-secondary">Back to Quotations</a>
        </div>
    </div>

    <script>
        document.getElementById('add-product').addEventListener('click', function() {
            let productList = document.getElementById('product-list');
            let newItem = productList.firstElementChild.cloneNode(true);
            newItem.querySelector('select').value = "";
            newItem.querySelector('input').value = "";
            productList.appendChild(newItem);

            let removeBtns = document.querySelectorAll('.remove-product');
            removeBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    if (document.querySelectorAll('.product-item').length > 1) {
                        this.parentElement.parentElement.remove();
                    }
                });
            });
        });
    </script>

</body>
</html>
