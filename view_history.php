<title>
    View Purchase History
</title>

<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require "includes/conn.php";
$id = $_GET["id"];

// $sql = "SELECT po.id, po.order_date, po.status, 
//         (SELECT SUM(subtotal) FROM purchase_order_items WHERE po_id = po.id) AS total_price
//         FROM purchase_orders po WHERE customer_id = $id";

$sql = "SELECT po.id, po.order_date, po.status, po.segment, po.sub_segment, p.project_name, a.agent_code,
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

<h2>Purchase History of <?php echo $fetch_name['name'] ?></h2>
<table class="table">
    <tr>
        <th>Purchase Order ID</th> <!-- Purchase Order ID dapat toh -->
        <th>Project Name</th>
        <th>Purchase Order Date</th>
        <th>Total Price</th>

        <!-- TENTATIVE PA -->
        <th>AGENT CODE</th>
        <th>SEGMENT</th>
        <th>SUBSEGMENT</th>


        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $row["id"] ?></td>
            <td><?= $row["project_name"] ?></td>
            <td><?= $row["order_date"] ?></td>
            <td>₱<?= $row["total_price"] ?></td>
            <!-- TENTATIVE PA -->
            <td><?= $row['agent_code'] ?></td>
            <td><?= $row['segment'] ?></td>
            <td><?= $row['sub_segment'] ?></td>
            <td><?= $row["status"] ?></td>
            <td>
                <a href="view_po.php?id=<?= $row["id"] ?>" class="btn btn-primary">View Details</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<a href="customer_information.php" class="btn btn-secondary">Back</a>

<?php

include "includes/footer.php";

?>