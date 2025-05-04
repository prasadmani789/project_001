<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);

    // Validate phone number (must be 10 digits)
    if (!preg_match('/^\d{10}$/', $phone)) {
        $error = "Phone number must be exactly 10 digits.";
    } 
    // Validate email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    }
    elseif (!empty($name) && !empty($address) && !empty($phone) && !empty($email)) {
        $stmt = $conn->prepare("INSERT INTO customers (name, address, phone, email) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $address, $phone, $email);
        
        if ($stmt->execute()) {
            $stmt->close();
            // Redirect to customer list page after successful insertion
            header("Location: customers.php");
            exit();
        } else {
            $error = "Error adding customer.";
        }
        $stmt->close();
    } else {
        $error = "All fields are required!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Customer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
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
                    <li class="nav-item"><a class="nav-link" href="customers.php">Customer Management</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-danger text-white" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Back Button at the Top Right -->
    <div class="container mt-3">
        <div class="d-flex justify-content-end">
            <a href="customers.php" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <!-- Add Customer Form -->
    <div class="container mt-3">
        <h2 class="text-center">Add New Customer</h2>
        <div class="card p-4 shadow-lg mt-4">
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" id="phone" class="form-control" maxlength="10" required>
                    <div id="phoneWarning" class="text-danger mt-1" style="display: none;">
                        Phone number must be exactly 10 digits.
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                    <div id="emailWarning" class="text-danger mt-1" style="display: none;">
                        Invalid email format.
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">Add Customer</button>
            </form>
        </div>
    </div>

    <script>
        // Phone number validation
        document.getElementById("phone").addEventListener("input", function () {
            var phoneField = this;
            var phoneWarning = document.getElementById("phoneWarning");
            var phoneValue = phoneField.value;

            // Allow only numbers
            phoneField.value = phoneValue.replace(/\D/g, '');

            if (phoneValue.length !== 10) {
                phoneWarning.style.display = "block";
                phoneField.classList.add("is-invalid");
            } else {
                phoneWarning.style.display = "none";
                phoneField.classList.remove("is-invalid");
            }
        });

        // Email validation
        document.getElementById("email").addEventListener("input", function () {
            var emailField = this;
            var emailWarning = document.getElementById("emailWarning");
            var emailValue = emailField.value;

            var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            if (!emailPattern.test(emailValue)) {
                emailWarning.style.display = "block";
                emailField.classList.add("is-invalid");
            } else {
                emailWarning.style.display = "none";
                emailField.classList.remove("is-invalid");
            }
        });
    </script>
</body>
</html>
