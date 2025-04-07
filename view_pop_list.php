<title>View Purchase Order Product List</title>

<?php

require "includes/conn.php";

$pop_list_id = $_GET['id'];

$pop_list_result = "SELECT *
                    FROM purchase_order_product_list popl
                    WHERE id = $pop_list_id";

$pop_list_query = mysqli_query($conn, $pop_list_result);

?>

<a href="po_products.php" class="btn btn-primary">Back</a>

<div>
    <h3>Purchase Order Product Lists</h3>
    <table class="table">
        <tr>
            <!-- <th>ID</th>
            <th>Purchase Order Product ID</th> -->
            <th>Date Received</th>
            <th>Product Code</th>
            <th>Lot Number</th>
            <th>Category</th>
            <th>Number of Cans</th>
            <th>Pack Size</th>
            <th>Liters</th>
            <th>Reorder Level</th>
            <th>Maintaining Level</th>
            <th>Expiration Date</th>
            <th>Manufacturer</th>
            <th>Vendor</th>
            <th>Description</th>
            <th>Notes</th>
            <th>SG</th>
        </tr>
        <?php if (mysqli_num_rows($pop_list_query) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($pop_list_query)): ?>
                <tr>
                    <!-- <td><?= $row['id'] ?></td>
                    <td><?= $row['pop_id'] ?></td> -->
                    <td><?= $row['date_received'] ?></td>
                    <td><?= $row['product_code'] ?></td>
                    <td><?= $row['lot_no'] ?></td>
                    <td><?= $row['category'] ?></td>
                    <td><?= $row['no_of_cans'] ?></td>
                    <td><?= $row['pack_size'] ?></td>
                    <td><?= $row['liters'] ?></td>
                    <td><?= $row['reorder_level'] ?></td>
                    <td><?= $row['maintaining_level'] ?></td>
                    <td><?= $row['expiration_date'] ?></td>
                    <td><?= $row['manufacturer'] ?></td>
                    <td><?= $row['vendor'] ?></td>
                    <td><?= $row['description'] ?></td>
                    <td><?= $row['notes'] ?></td>
                    <td><?= $row['sg'] ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <span>No list of product available.</span>
        <?php endif; ?>
    </table>
</div>

<?php

require "includes/footer.php";

?>



<!-- sobrang gulo ng naming convention ko, pero oks na yan -->