<title>Dashboard</title>

<?php
require "includes/conn.php";

function fetchSingleValue($conn, $query, $column)
{
    return mysqli_fetch_assoc(mysqli_query($conn, $query))[$column] ?? 0;
}

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
                        WHERE po.status != 'Completed'",
];

$data = [];
foreach ($queries as $key => $query) {
    $data[$key] = fetchSingleValue($conn, $query, strpos($query, "SUM") !== false ? "total" : "count");
}

// eto di ko pwede ilagay sas array kasi may specific condition na di ko pwede i single query
$resultRecentPOs = mysqli_query(
    $conn,
    "SELECT po.id, c.name AS customer_name, po.status, po.date_created,
     COALESCE((SELECT SUM(subtotal) FROM purchase_order_items WHERE po_id = po.id), 0) AS total_price
     FROM purchase_orders po 
     LEFT JOIN customers c ON po.customer_id = c.id 
     ORDER BY po.date_created DESC LIMIT 5"
);

$resultTopProducts = mysqli_query(
    $conn,
    "SELECT p.description, SUM(po_items.quantity) AS total_ordered FROM purchase_order_items po_items 
     LEFT JOIN products p ON po_items.product_id = p.id GROUP BY p.id ORDER BY total_ordered DESC LIMIT 5"
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
    $queryPOs = "SELECT po.id, c.name AS customer_name, a.agent_code AS agent_code, a.area, po.segment, po.sub_segment, po.date_created,
                 COALESCE((SELECT SUM(subtotal) FROM purchase_order_items WHERE po_id = po.id), 0) AS total_price
                 FROM purchase_orders po
                 JOIN customers c ON po.customer_id = c.id
                 JOIN agents a ON po.agent_id = a.id
                 WHERE po.status = 'Completed' 
                 AND YEAR(po.date_created) = $selectedYear 
                 AND MONTH(po.date_created) = $monthNumber 
                 ORDER BY po.date_created DESC";

    $resultPOs = mysqli_query($conn, $queryPOs);
    $monthlyPurchaseOrders[$monthNumber] = mysqli_fetch_all($resultPOs, MYSQLI_ASSOC);
}
?>




<body class="">
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

                    <div class="modal fade" id="modalMonth<?= $monthNumber ?>" tabindex="-1"
                        aria-labelledby="modalLabel<?= $monthNumber ?>" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalLabel<?= $monthNumber ?>">
                                        Purchase Orders - <?= $months[$monthNumber - 1] ?> <?= $selectedYear ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">

                                    <?php if (!empty($purchaseOrders)): ?>

                                        <!-- Agent Revenue Summary (Grouped by Area & Segment) -->
                                        <h5 class="mb-3"><strong>AGENT REVENUE SUMMARY</strong></h5>
                                        <table class="table table-bordered">
                                            <tbody>
                                                <?php
                                                $agentTotals = [];
                                                $allAgents = [];

                                                // Collect all unique agents
                                                foreach ($purchaseOrders as $po) {
                                                    $allAgents[$po['agent_code']] = true;
                                                }

                                                // Group revenues by area, segment, and agent
                                                foreach ($purchaseOrders as $po) {
                                                    $area = trim($po['area']);
                                                    $segment = trim($po['segment']);
                                                    $agent = trim($po['agent_code']);
                                                    $amount = floatval($po['total_price']);

                                                    // Determine agent suffix based on segment
                                                    $suffix = ($segment === 'PROTECTIVE') ? 'P' : (($segment === 'MARINE') ? 'M' : '');

                                                    // Append the correct suffix
                                                    $agentWithSuffix = $agent . $suffix;

                                                    if (!isset($agentTotals[$area])) {
                                                        $agentTotals[$area] = [];
                                                    }
                                                    if (!isset($agentTotals[$area][$segment])) {
                                                        $agentTotals[$area][$segment] = [];
                                                    }
                                                    if (!isset($agentTotals[$area][$segment][$agentWithSuffix])) {
                                                        $agentTotals[$area][$segment][$agentWithSuffix] = 0;
                                                    }
                                                    $agentTotals[$area][$segment][$agentWithSuffix] += $amount;
                                                }

                                                // Ensure all agents appear in every area & segment
                                                foreach ($agentTotals as $area => &$segments) {
                                                    foreach ($segments as $segment => &$agents) {
                                                        foreach ($allAgents as $agent => $value) {
                                                            $suffix = ($segment === 'PROTECTIVE') ? 'P' : (($segment === 'MARINE') ? 'M' : '');
                                                            $agentWithSuffix = $agent . $suffix;

                                                            if (!isset($agents[$agentWithSuffix])) {
                                                                $agents[$agentWithSuffix] = 0;
                                                            }
                                                        }
                                                    }
                                                }
                                                unset($segments, $agents); // Prevent accidental reference modification

                                                // Sort agent codes alphabetically before displaying
                                                foreach ($agentTotals as $area => &$segments) {
                                                    foreach ($segments as &$agents) {
                                                        ksort($agents);
                                                    }
                                                }
                                                unset($segments, $agents);


                                                foreach ($agentTotals as $area => $segments):
                                                    $areaTotal = 0; // Initialize total for the area
                                                    foreach ($segments as $segment => $agents) {
                                                        $areaTotal += array_sum($agents); // Add segment totals to area total
                                                    }
                                                ?>
                                                    <tr class="table-primary">
                                                        <td colspan="8">
                                                            <div class="d-flex justify-content-between">
                                                                <strong><?= strtoupper(htmlspecialchars($area)) ?></strong>
                                                                <span><strong>PhP </strong><?= number_format($areaTotal, 2) ?></span>
                                                                <span><strong>ltrs </strong><?= number_format($areaTotal, 2) ?></span> <!-- tentative -->
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php foreach ($segments as $segment => $agents):
                                                        $segmentTotal = array_sum($agents); // Calculate segment total
                                                    ?>
                                                        <tr class="table-secondary">
                                                            <td colspan="8" style="padding-left: 20px;">
                                                                <div class="d-flex justify-content-between">
                                                                    <strong>— <?= strtoupper(htmlspecialchars($segment)) ?></strong>
                                                                    <span><strong>PhP </strong><?= number_format($segmentTotal, 2) ?></span>
                                                                    <span><strong>ltrs </strong><?= number_format($segmentTotal, 2) ?></span> <!-- tentative -->
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <th>Agent Code</th>
                                                        <th>Quota</th>
                                                        <th>Quota / Volume(Liters)</th>
                                                        <!-- sunod na toh, may madadagdag din kasi sa column sa product -->
                                                        <?php foreach ($agents as $agent => $total): ?>
                                                            <tr>
                                                                <td style="padding-left: 40px;"><?= htmlspecialchars($agent) ?></td>
                                                                <td><?= number_format($total, 2) ?></td>
                                                                <td><?= number_format($total, 2) ?></td> <!-- tentative -->
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php endforeach; ?>
                                                <?php endforeach; ?>


                                            </tbody>
                                        </table>
                                        <!-- Detailed Customer Order Slip Table -->
                                        <h5 class="mt-4"><strong>DETAILED CUSTOMER ORDER SLIP</strong></h5>
                                        <table class="table table-bordered">
                                            <tbody>
                                                <?php
                                                $areas = ["Manila", "Cebu", "Gensan", "House"];
                                                $segments = ["Protective", "Marine"];

                                                foreach ($areas as $area): ?>
                                                    <tr class="table-primary">
                                                        <td colspan="8"><strong><?= $area ?></strong></td>
                                                    </tr>
                                                    <?php foreach ($segments as $segment): ?>
                                                        <tr class="table-secondary">
                                                            <td colspan="8" style="padding-left: 20px;"><strong>— <?= $segment ?></strong></td>
                                                        </tr>
                                                        <th>Customer Name</th>
                                                        <th>Agent Code</th>
                                                        <th>Area</th>
                                                        <th>Segment</th>
                                                        <th>Subsegment</th>
                                                        <th>Date Created</th>
                                                        <th>Total Price</th>
                                                        <?php
                                                        $hasData = false;

                                                        foreach ($purchaseOrders as $po):
                                                            if (strcasecmp(trim($po['area']), trim($area)) === 0 && strcasecmp(trim($po['segment']), trim($segment)) === 0):
                                                                $hasData = true; ?>
                                                                <tr>
                                                                    <td><?= htmlspecialchars($po['customer_name']) ?></td>
                                                                    <td><?= htmlspecialchars($po['agent_code']) ?></td>
                                                                    <td><?= htmlspecialchars($po['area']) ?></td>
                                                                    <td><?= htmlspecialchars($po['segment']) ?></td>
                                                                    <td><?= htmlspecialchars($po['sub_segment']) ?></td>
                                                                    <td><?= date('F d, Y', strtotime($po['date_created'])) ?></td>
                                                                    <td>₱<?= number_format($po['total_price'], 2) ?></td>
                                                                </tr>
                                                            <?php endif;
                                                        endforeach;

                                                        if (!$hasData): ?>
                                                            <tr>
                                                                <td colspan="8" class="text-muted text-center">No data available</td>
                                                            </tr>
                                                        <?php endif; ?>

                                                    <?php endforeach; ?>
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
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td><?= htmlspecialchars($row['total_ordered']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>
    </div>



    <script>
        document.getElementById('yearFilter').addEventListener('change', function() {
            window.location.href = "index.php?year=" + this.value;
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>


<?php include "includes/footer.php"; ?>