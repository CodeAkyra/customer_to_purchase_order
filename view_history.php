<title>
    View Purchase History
</title>

<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require "includes/conn.php";
$id = $_GET["id"];

// $sql = "SELECT po.id, po.date_of_cos, po.status, 
//         (SELECT SUM(subtotal) FROM purchase_order_items WHERE po_id = po.id) AS total_price
//         FROM purchase_orders po WHERE customer_id = $id";

$sql = "SELECT po.id, po.date_of_cos, po.status, po.segment, po.sub_segment, p.project_name, a.agent_code,
        (SELECT SUM(subtotal) FROM purchase_order_items WHERE po_id = po.id) AS total_price
        FROM purchase_orders po
        LEFT JOIN project p ON po.project_id = p.project_id
        JOIN agents a ON po.agent_id = a.id
        WHERE po.customer_id = $id";

$result = mysqli_query($conn, $sql);
?>

<?php

$sql = "SELECT name FROM customers WHERE id = $id";
$fetch_result = mysqli_query($conn, $sql);
$fetch_name = mysqli_fetch_assoc($fetch_result);

?>
<h2 class="text-center mb-4">Purchase History of <?php echo $fetch_name['name'] ?></h2>

<div class="container">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Purchase Order ID</th>
                <th>Project Name</th>
                <th>Date of COS</th>
                <th>Total Price</th>
                <th>Agent Code</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row["id"] ?></td>
                    <td><?= $row["project_name"] ?></td>
                    <td><?= $row["date_of_cos"] ?></td>
                    <td>₱<?= number_format($row["total_price"], 2) ?></td>
                    <td><?= $row['agent_code'] ?></td>
                    <td>
                        <span class="badge <?= $row['status'] == 'Completed' ? 'bg-success' : 'bg-secondary' ?>"><?= $row["status"] ?></span>
                    </td>
                    <td>
                        <a href="view_po.php?id=<?= $row["id"] ?>" class="btn btn-primary btn-sm">View</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="text-center mt-4">
        <a href="customer_information.php" class="btn btn-secondary">Back</a>
    </div>
</div>



<?php
include "includes/footer.php";

?>