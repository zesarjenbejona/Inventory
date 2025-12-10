<?php
include 'db.php';

$product_name = $_POST['product_name'] ?? '';
$quantity = $_POST['quantity'] ?? '';
$category = $_POST['category'] ?? '';
$price = $_POST['price'] ?? '';
$date_added = $_POST['date_added'] ?? '';

$sql = "INSERT INTO inventory (product_name, quantity, category, price, date_added) 
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sisss", $product_name, $quantity, $category, $price, $date_added);

$success = $stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Save Inventory Item</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body { background: #f4f8f3; }
        .card { border: none; }
        .card-header-success { background: linear-gradient(135deg,#28a745,#52c56b); color: white; }
        .nav-btns { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 20px; }
        .nav-btns .btn { margin-left: 0; }
        @media (min-width: 576px) { .nav-btns .btn { margin-left: 8px; } }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="card shadow-lg rounded-4">
        <div class="card-header card-header-success text-center fs-4 py-3 rounded-top-4">
            <i class="bi bi-save-fill"></i> Inventory Item Save Status
        </div>
        <div class="card-body p-4 text-center">

            <?php if ($success): ?>
                <div class="alert alert-success shadow-sm">
                    <i class="bi bi-check-circle-fill"></i> Item <strong><?= htmlspecialchars($product_name) ?></strong> saved successfully!
                </div>
            <?php else: ?>
                <div class="alert alert-danger shadow-sm">
                    <i class="bi bi-exclamation-triangle-fill"></i> Error saving item. Please try again.
                </div>
            <?php endif; ?>

            <!-- Navigation Buttons -->
            <div class="d-flex justify-content-center nav-btns mt-4">
                <a href="inventory_form.html" class="btn btn-success"><i class="bi bi-plus-circle"></i> Add Another Item</a>
                <a href="inventory_list.php" class="btn btn-primary"><i class="bi bi-card-list"></i> View Inventory List</a>
                <a href="reports.php" class="btn btn-warning text-dark"><i class="bi bi-bar-chart-line-fill"></i> View Reports</a>
            </div>

        </div>
    </div>
</div>

</body>
</html>
