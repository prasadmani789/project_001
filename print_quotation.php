<?php
require 'db_connect.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Quotation ID not provided!");
}

$quotation_id = intval($_GET['id']);

// Fetch quotation details
$query = "SELECT q.*, c.name, c.address, c.phone, c.email 
          FROM quotations q 
          JOIN customers c ON q.customer_id = c.id 
          WHERE q.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $quotation_id);
$stmt->execute();
$result = $stmt->get_result();
$quotation = $result->fetch_assoc();

// Fetch products
$product_query = "SELECT qi.*, p.name AS product_name, p.description, p.price, p.gst_percent 
                  FROM quotation_items qi
                  JOIN products p ON qi.product_id = p.id
                  WHERE qi.quotation_id = ?";
$stmt = $conn->prepare($product_query);
$stmt->bind_param("i", $quotation_id);
$stmt->execute();
$products = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 95%;
            max-width: 800px;
            margin: 0 auto;
            padding: 15px;
            border: 1px solid #000;
        }
        h2, h3 {
            text-align: center;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            table-layout: fixed; /* Ensures table respects width */
            word-wrap: break-word; /* Breaks long words */
        }
        table, th, td {
            border: 1px solid black;
            padding: 6px;
            text-align: center;
            font-size: 12px;
        }
        th {
            background-color: #f2f2f2;
        }
        .description-cell {
            text-align: left;
            min-width: 150px;
        }
        .footer {
            margin-top: 20px;
        }
        .terms {
            margin-top: 15px;
            font-size: 12px;
            line-height: 1.4;
        }
        .bank-details {
            margin-top: 20px;
            border: 1px solid black;
            padding: 10px;
            width: 100%;
            max-width: 400px;
            font-size: 12px;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .container {
                border: none;
                width: 100%;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            table {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin: 20px 0;">
        <button onclick="window.print()" class="btn btn-primary">Print Quotation</button>
        <a href="quotations.php" class="btn btn-secondary">Back to Quotations</a>
    </div>

    <div class="container">
        <h2>QUOTATION</h2>
        
        <!-- Quotation content remains the same -->
        <table>
            <tr>
                <th style="width: 5%">S.No.</th>
                <th style="width: 30%">Description</th>
                <th style="width: 8%">Qty</th>
                <th style="width: 10%">Price</th>
                <th style="width: 12%">Tax Base</th>
                <th style="width: 8%">GST %</th>
                <th style="width: 12%">GST Amt</th>
                <th style="width: 15%">Value</th>
            </tr>
            
            <tbody>
                <?php
                $serial = 1;
                $total_cost = 0;
                while ($row = $products->fetch_assoc()):
                    $tax_base = number_format($row['price'] * $row['quantity'], 2, '.', '');
                    $gst_amount = number_format(($tax_base * $row['gst_percent'] / 100), 2, '.', '');
                    $total_value = number_format(($tax_base + $gst_amount), 2, '.', '');
                    $total_cost += $total_value;
                ?>
                <tr>
                    <td><?= $serial ?></td>
                    <td class="description-cell">
                        <strong><?= htmlspecialchars($row['product_name']) ?></strong><br>
                        <?= htmlspecialchars($row['description']) ?>
                    </td>
                    <td><?= $row['quantity'] ?></td>
                    <td>₹<?= number_format($row['price'], 2) ?></td>
                    <td>₹<?= $tax_base ?></td>
                    <td><?= $row['gst_percent'] ?>%</td>
                    <td>₹<?= $gst_amount ?></td>
                    <td>₹<?= $total_value ?></td>
                </tr>
                <?php $serial++; endwhile; ?>
            </tbody>
            
            <tfoot>
                <tr>
                    <th colspan="6" class="text-end">Total Amount:</th>
                    <th colspan="2">₹<?= number_format($total_cost, 2) ?></th>
                </tr>
            </tfoot>
        </table>

       <div class="terms">
            <h3>Terms & Conditions</h3>
            <p>1. Transportation will be borne by the supplier.</p>
            <p>2. Delivery within 30 days after receiving a confirmed purchase order and advance payment.</p>
            <p>3. 50% advance against the work order, remaining 50% before delivery.</p>
        </div>

        <div class="bank-details">
            <h3>BANK DETAILS</h3>
            <p><strong>Bank Name:</strong> XYZ BANK OF INDIA</p>
            <p><strong>A/C No:</strong> 00001111100000</p>
			<p><strong>IFSC Code:</strong> XXXX0000</p>
            <p><strong>A/C Holder Name:</strong> XYZ Company Pvt Ltd</p>
        </div>

        <div class="footer">
            <p><strong>Date:</strong> <?php echo date("d-m-Y"); ?></p>
            <div style="text-align: right; margin-top: 20px;">
                <p><strong>Yours Faithfully,</strong></p>
                <p>[Your Company Name]</p>
            </div>
        </div>
    </div>
</body>
</html>