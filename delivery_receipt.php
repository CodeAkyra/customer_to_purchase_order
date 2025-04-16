<title>Delivery Receipt</title>
<?php

require "includes/conn.php";

$sqlApprove = "SELECT po.*, c.name AS customer_name, c.address AS customer_address, a.agent_code, p.project_name
                FROM purchase_orders po
                LEFT JOIN customers c ON po.customer_id = c.id
                LEFT JOIN agents a ON po.agent_id = a.id
                LEFT JOIN project p ON po.project_id = p.project_id
                -- WHERE po.status = 'Approved' naisip ko, what if naka display nalang din lahat pero naka priority yung mga dapat unang makita sa module na yun.
                GROUP BY po.id";

$approvedResult = mysqli_query($conn, $sqlApprove);
?>

<div>
    <h2 class="text-center mb-4">Delivery Receipt Module</h2>
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
        <?php if (mysqli_num_rows($approvedResult) > 0): ?>

            <?php while ($row = mysqli_fetch_assoc($approvedResult)): ?>
                <tr>
                    <td><?= $row['customer_name'] ?></td>
                    <td><?= $row['customer_address'] ?></td>
                    <td><?= $row['agent_code'] ?></td>
                    <td><?= $row['project_name'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td>
                        <a href="view_dr.php?id=<?= $row['id'] ?>" class="btn btn-primary">View</a>
                    </td>
                </tr>
            <?php endwhile; ?>

        <?php else: ?>
            <span>No approved orders available for delivery.</span>
        <?php endif; ?>
    </table>
</div>

<?php include "includes/footer.php"; ?>