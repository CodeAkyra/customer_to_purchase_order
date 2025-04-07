<title>Purchase Order Products</title>

<?php

require "includes/conn.php";

$po_products_list = "SELECT *
                    FROM purchase_order_product pop
                    LEFT JOIN purchase_order_product_list pl ON pl.pop_id = pop.id
                    GROUP BY pop.id";

$po_products_result = mysqli_query($conn, $po_products_list);

?>

<a href="create_pop.php" class="btn btn-primary">Create PO Product</a>
<div>
    <h3>Purchase Order Product</h3>
    <table class="table">
        <tr>
            <th>ID</th>
            <th>Purchase Order Description</th>
            <th>Purchase Order Date</th>
            <th>Purchase Order Price</th>
            <th>Purchase Order Status</th>
            <th>Action</th>
        </tr>
        <?php if (mysqli_num_rows($po_products_result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($po_products_result)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['po_description'] ?></td>
                    <td><?= $row['po_order_date'] ?></td>
                    <td><?= $row['po_price'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td><a href="view_pop_list.php?id=<?php echo $row['pop_id'] ?>" class="btn btn-primary">View</a></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <span> No purchase order product available. </span>
        <?php endif; ?>
    </table>
</div>

<div>

</div>

<?php include "includes/footer.php"; ?>