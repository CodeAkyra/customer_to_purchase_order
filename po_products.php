<title>Purchase Order Products</title>

<?php

require "includes/conn.php";

$po_products_list = "SELECT *
                    FROM purchase_order_product pop
                    LEFT JOIN purchase_order_product_list pl ON pl.pop_id = pop.id
                    ";

$po_products_result = mysqli_query($conn, $po_products_list);
$result = mysqli_fetch_assoc($po_products_result);

?>

<div>
    <strong>
        <span><?php echo $result['id'] ?></span>
        <span><?php echo $result['po_description'] ?></span>
        <span><?php echo $result['po_order_date'] ?></span>
        <span><?php echo $result['po_price'] ?></span>
        <span><a href="view_pop_list.php?id=<?php echo $result['pop_id'] ?>" class="btn btn-primary">View</a></span>
    </strong>
</div>

<div>

</div>

<?php include "includes/footer.php"; ?>