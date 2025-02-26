<title>View Project</title>
<?php
require "includes/conn.php";

$project_id = $_GET['id'];

// Handle project completion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date_ended = $_POST["date_ended"];

    if (!empty($date_ended)) {
        $update_sql = "UPDATE project SET date_ended = '$date_ended' WHERE project_id = $project_id";
        mysqli_query($conn, $update_sql);

        // Refresh page to reflect changes
        header("Location: view_project.php?id=$project_id");
        exit;
    }
}

// Fetch project details
$sql = "SELECT p.project_name, p.date_started, p.date_ended, c.name AS customer_name, c.address
        FROM project p
        JOIN customers c ON p.customer_id = c.id
        WHERE p.project_id = $project_id";

$result = mysqli_query($conn, $sql);
$project = mysqli_fetch_assoc($result);

// Fetch purchase orders related to this project
$po_sql = "SELECT po.id, po.order_date, po.status, 
                  (SELECT SUM(subtotal) FROM purchase_order_items WHERE po_id = po.id) AS total_price
           FROM purchase_orders po
           WHERE po.project_id = $project_id";

$po_result = mysqli_query($conn, $po_sql);

// Calculate total project price
$total_project_price = 0;
while ($po = mysqli_fetch_assoc($po_result)) {
    $total_project_price += $po["total_price"];
}

// Reset result set to reuse in table display
mysqli_data_seek($po_result, 0);
?>

<h2>Project Details</h2>
<p><strong>Project Name:</strong> <?= $project['project_name'] ?></p>
<p><strong>Customer Name:</strong> <?= $project['customer_name'] ?></p>
<p><strong>Customer Address:</strong> <?= $project['address'] ?></p>
<p><strong>Date Started:</strong> <?= $project['date_started'] ?></p>
<p><strong>Date Ended:</strong> <?= $project['date_ended'] ?: 'Not Completed' ?></p>

<h3>Purchase Orders</h3>
<table class="table">
    <tr>
        <th>Purchase Order ID</th>
        <th>Purchase Order Date</th>
        <th>Total Price</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php if (mysqli_num_rows($po_result) > 0): ?>
        <?php while ($po = mysqli_fetch_assoc($po_result)): ?>
            <tr>
                <td><?= $po["id"] ?></td>
                <td><?= $po["order_date"] ?></td>
                <td><?= number_format($po["total_price"], 2) ?></td>
                <td><?= $po["status"] ?></td>
                <td>
                    <a href="view_po.php?id=<?= $po["id"] ?>" class="btn btn-primary">View PO</a>
                </td>
            </tr>
        <?php endwhile; ?>
        <tr>
            <td colspan="2"><strong>Total Project Price:</strong></td>
            <td><strong>₱<?= number_format($total_project_price, 2) ?></strong></td>
            <td colspan="2"></td>
        </tr>
    <?php else: ?>
        <tr>
            <td colspan="5" class="text-center">No purchase orders found for this project.</td>
        </tr>
    <?php endif; ?>
</table>

<!-- Complete Project Form -->
<?php if (empty($project["date_ended"])): ?>
    <h3>Complete Project</h3>
    <form method="post">
        <label for="date_ended">Completion Date:</label>
        <input type="date" name="date_ended" required>
        <button type="submit" class="btn btn-success">Complete Project</button>
    </form>
<?php else: ?>
    <p class="text-success"><strong>This project has already been completed.</strong></p>
<?php endif; ?>

<a href="project.php" class="btn btn-secondary">Back</a>