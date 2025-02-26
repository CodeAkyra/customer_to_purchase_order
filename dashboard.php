<title>Dashboard</title>

<?php
require "includes/conn.php";

// Low stock count
$queryLowStock = "SELECT COUNT(*) AS low_stock_count FROM products WHERE stock < maintaining_level";
$lowStockCount = mysqli_fetch_assoc(mysqli_query($conn, $queryLowStock))["low_stock_count"];

// Total customers
$queryCustomers = "SELECT COUNT(*) AS total_customers FROM customers";
$totalCustomers = mysqli_fetch_assoc(mysqli_query($conn, $queryCustomers))["total_customers"];

// Active Projects (Projects that haven't ended)
$queryActiveProjects = "SELECT COUNT(*) AS active_projects FROM project WHERE date_ended IS NULL OR date_ended = ''";
$activeProjects = mysqli_fetch_assoc(mysqli_query($conn, $queryActiveProjects))["active_projects"];

// Completed Projects
$queryCompletedProjects = "SELECT COUNT(*) AS completed_projects FROM project WHERE date_ended IS NOT NULL AND date_ended != ''";
$completedProjects = mysqli_fetch_assoc(mysqli_query($conn, $queryCompletedProjects))["completed_projects"];

// Pending Purchase Orders
$queryPendingPO = "SELECT COUNT(*) AS pending_pos FROM purchase_orders WHERE status != 'Completed'";
$pendingPOs = mysqli_fetch_assoc(mysqli_query($conn, $queryPendingPO))["pending_pos"];

// Completed Purchase Orders
$queryCompletedPO = "SELECT COUNT(*) AS completed_pos FROM purchase_orders WHERE status = 'Completed'";
$completedPOs = mysqli_fetch_assoc(mysqli_query($conn, $queryCompletedPO))["completed_pos"];

// Total Revenue from Completed Purchase Orders
$queryTotalRevenue = "SELECT COALESCE(SUM(po_items.subtotal), 0) AS total_revenue
                      FROM purchase_orders po
                      JOIN purchase_order_items po_items ON po.id = po_items.po_id
                      WHERE po.status = 'Completed'";
$totalRevenue = mysqli_fetch_assoc(mysqli_query($conn, $queryTotalRevenue))["total_revenue"];

// Pending Project Expenses (Unpaid or Ongoing POs)
$queryPendingExpenses = "SELECT COALESCE(SUM(po_items.subtotal), 0) AS pending_expenses
                         FROM purchase_orders po
                         JOIN purchase_order_items po_items ON po.id = po_items.po_id
                         WHERE po.status != 'Completed'";
$pendingExpenses = mysqli_fetch_assoc(mysqli_query($conn, $queryPendingExpenses))["pending_expenses"];

// Recently Added Purchase Orders (Latest 5)
$queryRecentPOs = "SELECT po.id, c.name AS customer_name, po.status, po.date_created,
(SELECT SUM(subtotal) FROM purchase_order_items WHERE po_id = po.id) AS total_price
                   FROM purchase_orders po
                   JOIN customers c ON po.customer_id = c.id
                   ORDER BY po.date_created DESC
                   LIMIT 5";

$resultRecentPOs = mysqli_query($conn, $queryRecentPOs);

// Most Ordered Products
$queryTopProducts = "SELECT p.name, SUM(po_items.quantity) AS total_ordered
                     FROM purchase_order_items po_items
                     JOIN products p ON po_items.product_id = p.id
                     GROUP BY p.id
                     ORDER BY total_ordered DESC
                     LIMIT 5";
$resultTopProducts = mysqli_query($conn, $queryTopProducts);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="container mt-4">

    <h2 class="mb-4 text-center">DASHBOARD</h2>

    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-danger mb-3">
                <div class="card-header">Low Stock Products</div>
                <div class="card-body">
                    <h3 class="card-title"><?= $lowStockCount ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Total Customers</div>
                <div class="card-body">
                    <h3 class="card-title"><?= $totalCustomers ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Active Projects</div>
                <div class="card-body">
                    <h3 class="card-title"><?= $activeProjects ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-secondary mb-3">
                <div class="card-header">Completed Projects</div>
                <div class="card-body">
                    <h3 class="card-title"><?= $completedProjects ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header">Pending Purchase Orders</div>
                <div class="card-body">
                    <h3 class="card-title"><?= $pendingPOs ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Completed Purchase Orders</div>
                <div class="card-body">
                    <h3 class="card-title"><?= $completedPOs ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-dark mb-3">
                <div class="card-header">Total Revenue</div>
                <div class="card-body">
                    <h3 class="card-title">₱<?= number_format($totalRevenue, 2) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header">Pending Project Expenses</div>
                <div class="card-body">
                    <h3 class="card-title">₱<?= number_format($pendingExpenses, 2) ?></h3>
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
                            <th>Date</th>
                            <th>Total Price</th>
                            <th>Action</th>

                        </tr>
                        <?php while ($row = mysqli_fetch_assoc($resultRecentPOs)): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= $row['customer_name'] ?></td>
                                <td><?= $row['status'] ?></td>
                                <td><?= $row['date_created'] ?></td>
                                <td><?= $row["total_price"] ?></td>
                                <td>
                                    <a href="view_po.php?id=<?= $row["id"] ?>" class="btn btn-primary">View Details</a>
                                </td>
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
                                <td><?= $row['name'] ?></td>
                                <td><?= $row['total_ordered'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-3">
        <a href="dashboard.php" class="btn btn-primary">Dashboard</a>
        <a href="inventory.php" class="btn btn-primary">Inventory</a>
        <a href="index.php" class="btn btn-primary">Customer Information</a>
        <a href="project.php" class="btn btn-primary">Project</a>
    </div>

</body>

</html>