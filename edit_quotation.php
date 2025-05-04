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

// Fetch the quotation details
$query = $conn->prepare("SELECT * FROM quotations WHERE id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows == 0) {
    die("Quotation not found.");
}

$quotation = $result->fetch_assoc();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = $_POST['customer_id'];
    $date_requested = $_POST['date_requested'];

    $updateQuery = $conn->prepare("UPDATE quotations SET customer_id = ?, date_requested = ? WHERE id = ?");
    $updateQuery->bind_param("isi", $customer_id, $date_requested, $id);

    if ($updateQuery->execute()) {
        header("Location: quotations.php");
        exit();
    } else {
        echo "Error updating quotation.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Quotation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2>Edit Quotation</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Customer ID</label>
                <input type="number" name="customer_id" class="form-control" value="<?php echo htmlspecialchars($quotation['customer_id']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Date Requested</label>
                <input type="date" name="date_requested" class="form-control" value="<?php echo htmlspecialchars($quotation['date_requested']); ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Update Quotation</button>
            <a href="quotations.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
