<?php
// reports.php
// Requires: db.php (mysqli connection in $conn)

include 'db.php';

// Low stock threshold (change if needed)
$lowStockThreshold = 5;

// 1) Summary KPIs
$qTotalProducts = "SELECT COUNT(*) AS total_products FROM inventory";
$qTotalQuantity = "SELECT COALESCE(SUM(quantity),0) AS total_quantity FROM inventory";
$qTotalValue = "SELECT COALESCE(SUM(quantity * price),0) AS total_value FROM inventory";
$qCategories = "SELECT COUNT(DISTINCT category) AS total_categories FROM inventory";

$totalProducts = mysqli_fetch_assoc(mysqli_query($conn, $qTotalProducts))['total_products'] ?? 0;
$totalQuantity = mysqli_fetch_assoc(mysqli_query($conn, $qTotalQuantity))['total_quantity'] ?? 0;
$totalValue = mysqli_fetch_assoc(mysqli_query($conn, $qTotalValue))['total_value'] ?? 0.00;
$totalCategories = mysqli_fetch_assoc(mysqli_query($conn, $qCategories))['total_categories'] ?? 0;

// 2) Data for charts
// Items per category (for pie)
$qCategory = "SELECT category, COUNT(*) AS count FROM inventory GROUP BY category";
$resCategory = mysqli_query($conn, $qCategory);

$categories = [];
$categoryCounts = [];
while ($r = mysqli_fetch_assoc($resCategory)) {
    $categories[] = $r['category'];
    $categoryCounts[] = (int)$r['count'];
}

// Quantity per product (for bar)
$qProductQty = "SELECT product_name, quantity FROM inventory ORDER BY quantity DESC LIMIT 50";
$resProductQty = mysqli_query($conn, $qProductQty);

$productNames = [];
$productQuantities = [];
while ($r = mysqli_fetch_assoc($resProductQty)) {
    $productNames[] = $r['product_name'];
    $productQuantities[] = (int)$r['quantity'];
}

// 3) Low stock items
$qLowStock = "SELECT id, product_name, quantity FROM inventory WHERE quantity < $lowStockThreshold ORDER BY quantity ASC";
$resLowStock = mysqli_query($conn, $qLowStock);

$lowStockItems = [];
while ($r = mysqli_fetch_assoc($resLowStock)) {
    $lowStockItems[] = $r;
}

function format_currency($value) {
    return '₱' . number_format((float)$value, 2);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Grocery Inventory Dashboard</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { background: #f4f8f3; }
        .card-hero { background: linear-gradient(135deg,#ffba00,#ff7a00); color: white; }
        .card-green { background: linear-gradient(135deg,#28a745,#52c56b); color: white; }
        .card-blue { background: linear-gradient(135deg,#0d6efd,#4dabf7); color: white; }
        .card-purple { background: linear-gradient(135deg,#6f42c1,#9f7de3); color: white; }
        .kpi-number { font-size: 1.5rem; font-weight: 700; }
        .small-muted { color: rgba(255,255,255,0.85); }
        .chart-card { min-height: 360px; }
        .low-stock-badge { font-weight: 600; }
        .nav-btns .btn { margin-left: 8px; }
        @media (max-width: 575px) {
            .nav-btns { flex-direction: column; gap: 8px; }
            .nav-btns .btn { margin-left: 0; width: 100%; }
        }
    </style>
</head>
<body>

<div class="container mt-4">

    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h3 class="mb-0"><i class="bi bi-shop"></i> Grocery Inventory Dashboard</h3>
            <small class="text-muted">Real-time reports from your inventory database</small>
        </div>
        <div class="d-flex nav-btns">
            <a href="inventory_form.html" class="btn btn-success"><i class="bi bi-plus-circle"></i> Add Item</a>
            <a href="inventory_list.php" class="btn btn-primary"><i class="bi bi-card-list"></i> View Inventory</a>
            <a href="export_inventory.php" class="btn btn-outline-success"><i class="bi bi-download"></i> Export Excel</a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card card-green shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small-muted">Total Products</div>
                            <div class="kpi-number"><?= number_format($totalProducts); ?></div>
                        </div>
                        <div class="fs-2"><i class="bi bi-boxes"></i></div>
                    </div>
                    <div class="mt-2 small-muted">Distinct items in inventory</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-blue shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small-muted">Total Quantity</div>
                            <div class="kpi-number"><?= number_format($totalQuantity); ?></div>
                        </div>
                        <div class="fs-2"><i class="bi bi-stack"></i></div>
                    </div>
                    <div class="mt-2 small-muted">All stock units combined</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-purple shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small-muted">Inventory Value</div>
                            <div class="kpi-number"><?= format_currency($totalValue); ?></div>
                        </div>
                        <div class="fs-2"><i class="bi bi-currency-dollar"></i></div>
                    </div>
                    <div class="mt-2 small-muted">Estimated total stock value</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-hero shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small-muted">Categories</div>
                            <div class="kpi-number"><?= number_format($totalCategories); ?></div>
                        </div>
                        <div class="fs-2"><i class="bi bi-tags"></i></div>
                    </div>
                    <div class="mt-2 small-muted">Product categories</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Low-stock -->
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card chart-card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong>Quantity per Product</strong>
                    <small class="text-muted">Top items by quantity</small>
                </div>
                <div class="card-body">
                    <canvas id="barChart" style="max-height:420px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card chart-card shadow-sm mb-3">
                <div class="card-header bg-white">
                    <strong>Category Distribution</strong>
                </div>
                <div class="card-body">
                    <canvas id="pieChart" style="max-height:300px;"></canvas>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <strong>Low Stock Alerts</strong>
                    <small class="text-muted ms-2">threshold: &lt; <?= $lowStockThreshold ?></small>
                </div>
                <div class="card-body">
                    <?php if (count($lowStockItems) === 0): ?>
                        <div class="alert alert-success mb-0">No low-stock items 🎉</div>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($lowStockItems as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($item['product_name']); ?></div>
                                        <small class="text-muted">ID: <?= $item['id']; ?></small>
                                    </div>
                                    <span class="badge bg-danger rounded-pill low-stock-badge"><?= $item['quantity']; ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional: raw data preview -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <strong>Recent Inventory Items</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Date Added</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $qRecent = "SELECT * FROM inventory ORDER BY date_added DESC, id DESC LIMIT 15";
                                $resRecent = mysqli_query($conn, $qRecent);
                                while ($r = mysqli_fetch_assoc($resRecent)):
                                ?>
                                <tr>
                                    <td><?= $r['id']; ?></td>
                                    <td><?= htmlspecialchars($r['product_name']); ?></td>
                                    <td><?= $r['quantity']; ?></td>
                                    <td><?= htmlspecialchars($r['category']); ?></td>
                                    <td><?= format_currency($r['price']); ?></td>
                                    <td><?= $r['date_added']; ?></td>
                                </tr>
                                <?php endwhile; ?>
                                <?php if (mysqli_num_rows($resRecent) === 0): ?>
                                <tr><td colspan="6" class="text-center small text-muted py-3">No inventory records yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Scripts -->
<script>
    // Chart data from PHP
    const productLabels = <?= json_encode($productNames, JSON_UNESCAPED_UNICODE); ?>;
    const productQtys = <?= json_encode($productQuantities); ?>;

    const categoryLabels = <?= json_encode($categories, JSON_UNESCAPED_UNICODE); ?>;
    const categoryCounts = <?= json_encode($categoryCounts); ?>;

    // BAR CHART - Quantity per product
    const barCtx = document.getElementById('barChart').getContext('2d');
    const barChart = new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: productLabels,
            datasets: [{
                label: 'Quantity',
                data: productQtys,
                borderWidth: 1,
                backgroundColor: function(context) {
                    const i = context.dataIndex;
                    const colors = ['#52c56b','#4dabf7','#ffc857','#ff7a00','#8a63d2'];
                    return colors[i % colors.length];
                }
            }]
        },
        options: {
            indexAxis: 'x',
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { precision:0 } }
            }
        }
    });

    // PIE CHART - Category distribution
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    const pieChart = new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: categoryCounts,
                backgroundColor: [
                    '#52c56b','#4dabf7','#ffc857','#ff7a00','#8a63d2','#ff6384','#36a2eb'
                ]
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
</script>

</body>
</html>
