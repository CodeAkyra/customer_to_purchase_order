<title>Product Transaction</title>
<!-- Tentative Title Page -->

<?php

require "includes/conn.php";

$product_id = $_GET['id'];

$poIQuery = "SELECT po_i.product_id, po_i.id, po_i.po_id, po_i.quantity, po_i.price, po_i.subtotal, p.name, po.customer_id as customer_name
                FROM purchase_order_items po_i
                LEFT JOIN purchase_orders po ON po_i.po_id = po.id
                LEFT JOIN products p ON po_i.product_id = p.id
                WHERE po_i.product_id = $product_id";

$poIResult = mysqli_query($conn, $poIQuery);

?>

<a href="inventory.php" class="btn btn-primary">Return</a>

<table class="table">
    <tr>
        <th>Purchase Order ID</th>
        <th>Customer Name</th>
                <!-- Customer Name dapat -->
 
        <th>Product Name</th> 
        <th>Quantity</th>
        <th>Price</th>
        <th>Subtotal</th>
        <th>Action</th>
    </tr>

    <?php

if (mysqli_num_rows($poIResult) > 0){
    while($row = mysqli_fetch_array($poIResult)){
        echo "<tr>
        <td> " . htmlspecialchars( $row["po_id"]) . " </td> <!-- tentative -->
        <td> " . htmlspecialchars( $row["customer_name"]) . " </td>
        <td> " . htmlspecialchars( $row["name"]) . " </td>
        <td> " . htmlspecialchars( $row["quantity"]) . " </td>
        <td> " . htmlspecialchars( $row["price"]) . " </td>
        <td> " . htmlspecialchars( $row["subtotal"]) . " </td>
        <td>
        <a href='view_po.php?id=" . $row['po_id'] . "' class='btn btn-outline-dark' >View</a>
        </td>
        </tr>";
    }
}

?>
</table>