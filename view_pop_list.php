<title>View Purchase Order Product List</title>

<?php

require "includes/conn.php";

$pop_list_id = $_GET['id'];

$pop_list_result = "SELECT *
                    FROM purchase_order_product_list popl
                    WHERE id = $pop_list_id";

$pop_list_query = mysqli_query($conn, $pop_list_result);

$result = mysqli_fetch_assoc($pop_list_query);

?>

<a href="po_products.php" class="btn btn-primary">Back</a>

<div>
    <span><?php echo $result['id'] ?></span>
    <span><?php echo $result['pop_id'] ?></span>
    <span><?php echo $result['date_received'] ?></span>
    <span><?php echo $result['product_code'] ?></span>
    <span><?php echo $result['lot_no'] ?></span>
    <span><?php echo $result['category'] ?></span>
    <span><?php echo $result['no_of_cans'] ?></span>
    <span><?php echo $result['pack_size'] ?></span>
    <span><?php echo $result['liters'] ?></span>
    <span><?php echo $result['reorder_level'] ?></span>
    <span><?php echo $result['maintaining_level'] ?></span>
    <span><?php echo $result['expiration_date'] ?></span>
    <span><?php echo $result['manufacturer'] ?></span>
    <span><?php echo $result['vendor'] ?></span>
    <span><?php echo $result['description'] ?></span>
    <span><?php echo $result['notes'] ?></span>
    <span><?php echo $result['sg'] ?></span>
</div>



<?php

require "includes/footer.php";

?>



<!-- sobrang gulo ng naming convention ko, pero oks na yan -->