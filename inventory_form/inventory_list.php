<?php 
include 'db.php';

// Fetch inventory data
$result = $conn->query("SELECT * FROM inventory");

// KPIs
$totalProducts = mysqli_fetch_assoc($conn->query("SELECT COUNT(*) AS total FROM inventory"))['total'] ?? 0;
$totalQuantity = mysqli_fetch_assoc($conn->query("SELECT COALESCE(SUM(quantity),0) AS total_quantity FROM inventory"))['total_quantity'] ?? 0;
$totalValue = mysqli_fetch_assoc($conn->query("SELECT COALESCE(SUM(quantity*price),0) AS total_value FROM inventory"))['total_value'] ?? 0;
$totalCategories = mysqli_fetch_assoc($conn->query("SELECT COUNT(DISTINCT category) AS total_categories FROM inventory"))['total_categories'] ?? 0;

function format_currency($value) {
    return '₱' . number_format((float)$value, 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Records</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body { background: #f4f8f3; }
        .card { border: none; }
        .nav-btns { flex-wrap: wrap; gap: 8px; margin-bottom: 20px; }
        .nav-btns .btn { margin-left: 8px; }
        .kpi-card { min-height: 120px; color: white; }
        .kpi-number { font-size: 1.5rem; font-weight: 700; }
        .dataTables_wrapper .dt-buttons { margin-bottom: 10px; }
        @media (max-width: 575px) { .nav-btns { flex-direction: column; } .nav-btns .btn { margin-left: 0; width: 100%; } }
    </style>
</head>
<body>

<div class="container mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h3><i class="bi bi-card-list"></i> Inventory Records</h3>
        <div class="d-flex nav-btns">
            <a href="inventory_form.html" class="btn btn-success"><i class="bi bi-plus-circle"></i> Add Item</a>
            <a href="reports.php" class="btn btn-warning text-dark"><i class="bi bi-bar-chart-line-fill"></i> View Reports</a>
            <a href="export_inventory.php" class="btn btn-outline-success"><i class="bi bi-download"></i> Export Excel</a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm kpi-card" style="background: #52c56b;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">Total Products</div>
                        <div class="kpi-number"><?= $totalProducts ?></div>
                    </div>
                    <i class="bi bi-boxes fs-2"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm kpi-card" style="background: #4dabf7;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">Total Quantity</div>
                        <div class="kpi-number"><?= $totalQuantity ?></div>
                    </div>
                    <i class="bi bi-stack fs-2"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm kpi-card" style="background: #ffba00;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">Inventory Value</div>
                        <div class="kpi-number"><?= format_currency($totalValue) ?></div>
                    </div>
                    <i class="bi bi-currency-dollar fs-2"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm kpi-card" style="background: #6f42c1;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">Categories</div>
                        <div class="kpi-number"><?= $totalCategories ?></div>
                    </div>
                    <i class="bi bi-tags fs-2"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory DataTable -->
    <div class="card shadow-lg rounded-4">
        <div class="card-body p-4">
            <table id="inventoryTable" class="table table-striped table-hover table-bordered rounded-3">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Date Added</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['product_name']); ?></td>
                        <td><?= $row['quantity']; ?></td>
                        <td><?= htmlspecialchars($row['category']); ?></td>
                        <td><?= format_currency($row['price']); ?></td>
                        <td><?= $row['date_added']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#inventoryTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                text: 'Export to Excel',
                className: 'btn btn-success',
                action: function () {
                    window.location.href = 'export_inventory.php';
                }
            }
        ]
    });
});
</script>

</body>
</html>
