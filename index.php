<title>Dashboard</title>

<?php
require "includes/conn.php";

function fetchSingleValue($conn, $query, $column)
{
    return mysqli_fetch_assoc(mysqli_query($conn, $query))[$column] ?? 0;
}

// mark eto suggested ko na code. mas efficient siya. try mo din na eto iimplement. ganto ginawa ko sa main repo
// array is da key, array is the GOAT
$queries = [
    "lowStockCount" => "SELECT COUNT(*) AS count FROM products WHERE stock < maintaining_level",
    "totalCustomers" => "SELECT COUNT(*) AS count FROM customers",
    "activeProjects" => "SELECT COUNT(*) AS count FROM project WHERE date_ended IS NULL OR date_ended = ''",
    "completedProjects" => "SELECT COUNT(*) AS count FROM project WHERE date_ended IS NOT NULL AND date_ended != ''",
    "pendingPOs" => "SELECT COUNT(*) AS count FROM purchase_orders WHERE status != 'Completed'",
    "completedPOs" => "SELECT COUNT(*) AS count FROM purchase_orders WHERE status = 'Completed'",
    "totalRevenue" => "SELECT COALESCE(SUM(po_items.subtotal), 0) AS total FROM purchase_orders po 
                       LEFT JOIN purchase_order_items po_items ON po.id = po_items.po_id WHERE po.status = 'Completed'",
    "pendingExpenses" => "SELECT COALESCE(SUM(po_items.subtotal), 0) AS total 
                        FROM purchase_orders po 
                        LEFT JOIN purchase_order_items po_items ON po.id = po_items.po_id 
                        WHERE po.status != 'Completed'"
];

// call the GOAT aka array
$data = [];
foreach ($queries as $key => $query) {
    $data[$key] = fetchSingleValue($conn, $query, strpos($query, "SUM") !== false ? "total" : "count");
}

// eto di ko pwede ilagay sas array kasi may specific condition na di ko pwede i single query
$resultRecentPOs = mysqli_query(
    $conn,
    "SELECT po.id, c.name AS customer_name, po.status, po.date_created,
     COALESCE((SELECT SUM(subtotal) FROM purchase_order_items WHERE po_id = po.id), 0) AS total_price
     FROM purchase_orders po JOIN customers c ON po.customer_id = c.id 
     ORDER BY po.date_created DESC LIMIT 5"
);

$resultTopProducts = mysqli_query(
    $conn,
    "SELECT p.name, SUM(po_items.quantity) AS total_ordered FROM purchase_order_items po_items 
     JOIN products p ON po_items.product_id = p.id GROUP BY p.id ORDER BY total_ordered DESC LIMIT 5"
);

$selectedYear = $_GET['year'] ?? date('Y');

$resultYears = mysqli_query($conn, "SELECT DISTINCT YEAR(date_created) AS year FROM purchase_orders ORDER BY year DESC");

$resultMonthlySales = mysqli_query(
    $conn,
    "SELECT MONTH(po.date_created) AS month, COALESCE(SUM(po_items.subtotal), 0) AS total_sales 
     FROM purchase_orders po LEFT JOIN purchase_order_items po_items ON po.id = po_items.po_id 
     WHERE po.status = 'Completed' AND YEAR(po.date_created) = $selectedYear 
     GROUP BY MONTH(po.date_created) ORDER BY MONTH(po.date_created)"
);

$salesData = array_fill(1, 12, 0);
while ($row = mysqli_fetch_assoc($resultMonthlySales)) {
    $salesData[$row['month']] = $row['total_sales'];
}

$monthlyPurchaseOrders = [];
for ($monthNumber = 1; $monthNumber <= 12; $monthNumber++) {
    $queryPOs = "SELECT po.id, c.name AS customer_name, po.date_created,
                 COALESCE((SELECT SUM(subtotal) FROM purchase_order_items WHERE po_id = po.id), 0) AS total_price
                 FROM purchase_orders po
                 JOIN customers c ON po.customer_id = c.id
                 WHERE po.status = 'Completed' 
                 AND YEAR(po.date_created) = $selectedYear 
                 AND MONTH(po.date_created) = $monthNumber 
                 ORDER BY po.date_created DESC";

    $resultPOs = mysqli_query($conn, $queryPOs);
    $monthlyPurchaseOrders[$monthNumber] = mysqli_fetch_all($resultPOs, MYSQLI_ASSOC);
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="container mt-4">
    <h2 class="mb-4 text-center">DASHBOARD</h2>
    <div class="row">
        <?php
        $cards = [
            ["Low Stock Products", $data["lowStockCount"], "danger"],
            ["Total Customers", $data["totalCustomers"], "primary"],
            ["Active Projects", $data["activeProjects"], "success"],
            ["Completed Projects", $data["completedProjects"], "secondary"],
            ["Pending Purchase Orders", $data["pendingPOs"], "warning"],
            ["Completed Purchase Orders", $data["completedPOs"], "success"],
            ["Total Revenue", "₱" . number_format($data["totalRevenue"], 2), "dark"],
            ["Pending Project Expenses", "₱" . number_format($data["pendingExpenses"], 2), "warning"]
        ];
        foreach ($cards as [$title, $count, $color]): ?>
            <div class="col-md-4">
                <div class="card text-white bg-<?= $color ?> mb-3">
                    <div class="card-header"> <?= htmlspecialchars($title) ?> </div>
                    <div class="card-body">
                        <h3 class="card-title"> <?= htmlspecialchars($count) ?> </h3>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Yearly Sales Forecast</span>
                <select id="yearFilter" class="form-select w-auto">
                    <?php while ($row = mysqli_fetch_assoc($resultYears)): ?>
                        <option value="<?= $row['year'] ?>" <?= ($row['year'] == $selectedYear) ? 'selected' : '' ?>>
                            <?= $row['year'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="card-body row">
                <?php
                $months = [
                    "January",
                    "February",
                    "March",
                    "April",
                    "May",
                    "June",
                    "July",
                    "August",
                    "September",
                    "October",
                    "November",
                    "December"
                ];

                for ($monthNumber = 1; $monthNumber <= 12; $monthNumber++):
                    $totalSales = $salesData[$monthNumber] ?? 0;
                    $purchaseOrders = $monthlyPurchaseOrders[$monthNumber] ?? [];
                ?>
                    <div class="col-md-3 mb-3">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalMonth<?= $monthNumber ?>"
                            class="text-decoration-none">
                            <div class="card shadow-sm text-center p-3 cursor-pointer">
                                <h5 class="card-title"><?= $months[$monthNumber - 1] ?></h5>
                                <p class="card-text fs-4 fw-bold">₱<?= number_format($totalSales, 2) ?></p>
                            </div>
                        </a>
                    </div>

                    <!-- modal -->

                    <!-- gets ko na paano yung magiging output neto, nag eexperiment pa muna ako sa ibang prototype -->
                    <!-- para yun lang yung focus niya and makuha ko agad yung gusto ko na output -->
                    <!-- if trip niyo ma imagine, open niyo yung file STI-IPI(Sales Evaluation Template) then open niyo sa baba yung February 21 -->
                    <div class="modal fade" id="modalMonth<?= $monthNumber ?>" tabindex="-1"
                        aria-labelledby="modalLabel<?= $monthNumber ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalLabel<?= $monthNumber ?>">
                                        Purchase Orders - <?= $months[$monthNumber - 1] ?> <?= $selectedYear ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <?php if (!empty($purchaseOrders)): ?>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>PO ID</th>
                                                    <th>Agents</th>
                                                    <th>Area</th>
                                                    <th>Segment</th>
                                                    <th>Subsegment</th>
                                                    <th>Customer</th>
                                                    <th>Date Created</th>
                                                    <th>Total Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($purchaseOrders as $po): ?>
                                                    <tr>
                                                        <td><?= $po['id'] ?></td>https://github.com/jehanalexis/CAPSTONE-2.1.git
                                                        <td>NULL</td>
                                                        <td>NULL</td>
                                                        <td>NULL</td>
                                                        <td>NULL</td>
                                                        <td><?= $po['customer_name'] ?></td>
                                                        <td><?= date('F d, Y', strtotime($po['date_created'])) ?></td>
                                                        <td>₱<?= number_format($po['total_price'], 2) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    <?php else: ?>
                                        <p class="text-center text-muted">No Purchase Orders found for this month.</p>
                                    <?php endif; ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>

        </div>
    </div>


    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-header">Recent Purchase Orders</div>
            <div class="card-body">
                <table class="table table-striped">
                    <tr>
                        <th>PO ID</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Date Created</th>
                        <th>Total Price</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($resultRecentPOs)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['customer_name']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td><?= htmlspecialchars($row['date_created']) ?></td>
                            <td>₱<?= number_format($row["total_price"], 2) ?></td>
                            <td><a href="view_po.php?id=<?= $row['id'] ?>" class="btn btn-primary">View Details</a></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-header">Top 5 Ordered Products</div>
            <div class="card-body">
                <table class="table table-striped">
                    <tr>
                        <th>Product</th>
                        <th>Total Ordered</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($resultTopProducts)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['total_ordered']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>
    </div>



    <div class="text-center mt-3">
        <a href="index.php" class="btn btn-primary">Dashboard</a>
        <a href="inventory.php" class="btn btn-primary">Inventory</a>
        <a href="customer_information.php" class="btn btn-primary">Customer Information</a>
        <a href="project.php" class="btn btn-primary">Project</a>
    </div>

    <script>
        document.getElementById('yearFilter').addEventListener('change', function() {
            window.location.href = "index.php?year=" + this.value;
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>