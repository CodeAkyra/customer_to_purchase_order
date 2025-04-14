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
$po_sql = "SELECT po.id, po.date_of_cos, po.status, 
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
<div class="container mt-4">

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Project Details</h4>
        </div>
        <div class="card-body">
            <p><strong>Project Name:</strong> <?= htmlspecialchars($project['project_name']) ?></p>
            <p><strong>Customer Name:</strong> <?= htmlspecialchars($project['customer_name']) ?></p>
            <p><strong>Customer Address:</strong> <?= htmlspecialchars($project['address']) ?></p>
            <p><strong>Date Started:</strong> <?= htmlspecialchars($project['date_started']) ?></p>
            <p><strong>Date Ended:</strong> <?= $project['date_ended'] ? htmlspecialchars($project['date_ended']) : '<span class="text-danger">Not Completed</span>' ?></p>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Purchase Orders</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">PO ID</th>
                        <th scope="col">PO Date</th>
                        <th scope="col">Total Price</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($po_result) > 0): ?>
                        <?php while ($po = mysqli_fetch_assoc($po_result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($po["id"]) ?></td>
                                <td><?= htmlspecialchars($po["date_of_cos"]) ?></td>
                                <td>₱<?= number_format($po["total_price"], 2) ?></td>
                                <td><?= htmlspecialchars($po["status"]) ?></td>
                                <td>
                                    <a href="view_po.php?id=<?= $po["id"] ?>" class="btn btn-sm btn-outline-primary">View PO</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <tr class="table-light fw-bold">
                            <td colspan="2">Total Project Price:</td>
                            <td>₱<?= number_format($total_project_price, 2) ?></td>
                            <td colspan="2"></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No purchase orders found for this project.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (empty($project["date_ended"])): ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5>Complete Project</h5>
                <form method="post" class="row g-2 align-items-center">
                    <div class="col-auto">
                        <label for="date_ended" class="col-form-label">Completion Date:</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="date_ended" class="form-control" required>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-success">Mark as Completed</button>
                    </div>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-success" role="alert">
            <strong>This project has already been completed.</strong>
        </div>
    <?php endif; ?>

    <a href="project.php" class="btn btn-outline-secondary">← Back to Projects</a>
</div>


<?php include "includes/footer.php"; ?>