<title>Customer Order Slip</title>
<?php

require "includes/conn.php";

$sqlPending = "SELECT po.*, c.name AS customer_name, c.address AS customer_address, a.agent_code, p.project_name
                FROM purchase_orders po
                LEFT JOIN customers c ON po.customer_id = c.id
                LEFT JOIN agents a ON po.agent_id = a.id
                LEFT JOIN project p ON po.project_id = p.project_id
                -- WHERE po.status = 'Pending'
                GROUP BY po.id";

$pendingResult = mysqli_query($conn, $sqlPending);
?>

<div>
    <h2 class="text-center mb-4">Customer Order Slip</h2>
    <table class="table">
        <tr>
            <th>Customer</th>
            <th>Address</th>
            <th>Agent</th>
            <!-- From Agent to TSR(Technical Sales Representative) -->
            <th>Project Name</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php if (mysqli_num_rows($pendingResult) > 0): ?>

            <?php while ($row = mysqli_fetch_assoc($pendingResult)): ?>
                <tr>
                    <td><?= $row['customer_name'] ?></td>
                    <td><?= $row['customer_address'] ?></td>
                    <td><?= $row['agent_code'] ?></td>
                    <td><?= $row['project_name'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td>
                        <a href="view_po.php?id=<?= $row['id'] ?>" class="btn btn-primary">View</a>
                    </td>
                </tr>
            <?php endwhile; ?>

        <?php else: ?>
            <span>No pending customer order slip available.</span>
        <?php endif; ?>
    </table>
</div>

<?php include "includes/footer.php"; ?>


<!-- dapat narerecord na yung downpayment ng customer dito -->