<?php
require "../includes/conn.php";

$pop_list_id = (int) $_GET['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transfer_id'])) {
    $transfer_id = (int) $_POST['transfer_id'];

    // Fetch the item based on the transfer ID
    $fetchItem = mysqli_query($conn, "SELECT * FROM purchase_order_product_list WHERE id = $transfer_id");

    if ($item = mysqli_fetch_assoc($fetchItem)) {
        // Debugging: Output the fetched item
        var_dump($item); // Check the values of product_code_ref, lot_no, and no_of_cans

        // Check for NULL values
        if (is_null($item['lot_no']) || is_null($item['no_of_cans'])) {
            die("One of the fields is NULL.");
        }

        // Use the correct field for product_code_ref
        $product_code_ref = $item['product_code']; // Change this line

        // Check if the product_code already exists in product_list
        $checkProduct = mysqli_query($conn, "SELECT * FROM product_list WHERE product_code = '$product_code_ref'");

        // If it doesn't exist, insert it into product_list
        if (mysqli_num_rows($checkProduct) === 0) {
            $insertProductStmt = $conn->prepare("INSERT INTO product_list (product_code) VALUES (?)");
            $insertProductStmt->bind_param("s", $product_code_ref);
            $insertProductStmt->execute();
            $insertProductStmt->close();
        }

        // Prepare the SQL statement for product_list_ln
        $stmt = $conn->prepare("INSERT INTO product_list_ln (product_code_ref, lot_no, no_of_cans, pack_size, liters, description) VALUES (?, ?, ?, ?, ?, ?)");

        // Bind parameters
        $stmt->bind_param(
            "ssidss", // Assuming product_code_ref is a string and no_of_cans is an integer
            $product_code_ref,
            $item['lot_no'],
            $item['no_of_cans'],
            $item['pack_size'],
            $item['liters'],
            $item['description']
        );

        // Execute the statement
        if ($stmt->execute()) {
            // Update the status after successful insertion
            mysqli_query($conn, "UPDATE purchase_order_product_list SET transfer_status = 'Transferred' WHERE id = $transfer_id");

            header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $pop_list_id);
            exit;
        }

        $stmt->close();
    } else {
        // Handle case where no item was fetched
        die("No item found with the given transfer ID.");
    }
}

$pop_list_query = mysqli_query($conn, "SELECT * FROM purchase_order_product_list WHERE pop_id = $pop_list_id");

?>

<a href="po_products.php" class="btn btn-primary">Back</a>

<div>
    <h3>Purchase Order Product Lists</h3>
    <table class="table">
        <tr>
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
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php if (mysqli_num_rows($pop_list_query) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($pop_list_query)): ?>
                <tr style="width: 50px; height: 100px;">
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
                    <td><?= $row['transfer_status'] ?? 'Pending' ?></td>
                    <td>
                        <?php if (($row['transfer_status'] ?? 'Pending') === 'Pending'): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="transfer_id" value="<?= $row['id'] ?>">
                                <button type="submit" class="btn btn-success btn-sm">Transfer to Inventory</button>
                                <!-- dapat meron dito search din -->
                                <!-- eto na yung purpose ng pag add ng quantity sa inventory -->
                            </form>
                        <?php else: ?>
                            <span class="badge bg-secondary">Transferred</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="17">No list of product available.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

<?php require "../includes/footer.php"; ?>


<!-- insert from table to table -->
<!-- yung inventory table sa database prototype palang yun, tinitignan q kung paano ko malalaro yung items dun etcetc.. -->


<!-- lahat ng product na nakalista dito matratransfer sa inventory, pero mag sstay parin dito yung data for record purposes nalang siya -->
<!-- may search filter din ditu, pwedeng using product_code and lot_no. and dito narin papasok si barcode scanning. -->


<!-- PALITAN NIYO NALANG YUNG STATUS FROM TRANSFERRED TO PENDING -->
<!-- PALITAN NIYO NALANG YUNG STATUS FROM TRANSFERRED TO PENDING -->
<!-- PALITAN NIYO NALANG YUNG STATUS FROM TRANSFERRED TO PENDING -->
<!-- PALITAN NIYO NALANG YUNG STATUS FROM TRANSFERRED TO PENDING -->
<!-- PALITAN NIYO NALANG YUNG STATUS FROM TRANSFERRED TO PENDING -->