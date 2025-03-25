<title>Pre-Inventory</title>

<?php
require "includes/conn.php";

$productQuery = "SELECT product_status FROM products WHERE product_status = 'draft' LIMIT 1";
$productResult = mysqli_query($conn, $productQuery);
$verify = mysqli_fetch_assoc($productResult);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_product'])) {
    $updateQuery = "UPDATE products SET product_status = 'verified' WHERE product_status = 'draft'";
    $updateResult = mysqli_query($conn, $updateQuery);

    if ($updateResult) {
        echo "<p class='text-success'><strong>Products Verified!</strong></p>";
        echo "<meta http-equiv='refresh' content='0'>"; // Refresh the page to reflect changes
    } else {
        echo "Error updating product: " . mysqli_error($conn);
    }
}
?>

<div>
    <a href="inventory.php" class="btn btn-danger">Return</a>

    <!-- Add Product Button (Opens Modal) -->
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">+ Add Product</button>

    <?php if (!empty($verify) && $verify['product_status'] == "Draft"): ?>
        <form method="post" style="display: inline;">
            <button type="submit" name="verify_product" class="btn btn-success">Verify</button>
        </form>
    <?php else: ?>
        <p class="text-success"><strong>No draft</strong></p>
    <?php endif; ?>
</div>

<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Add New Product | Date received <?php echo date('Y-m-d') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    <label>Serial Code</label> <span style="color: #ADADAD;">*Connect a barcode scanner for quicker input*</span>
                    <input type="text" name="serial_code" class="form-control" required placeholder="Enter Serial Code">

                    <label>Lot Number</label> <span style="color: #ADADAD;">*Connect a barcode scanner for quicker input*</span>
                    <input type="text" name="lot_no" class="form-control" required placeholder="Enter Lot Number">

                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="Enter Product Name">

                    <label>Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" required placeholder="Enter Price">

                    <label>Stock</label>
                    <input type="number" name="stock" class="form-control" required placeholder="Enter Stock Quantity">

                    <label>Maintaining Level</label>
                    <input type="number" name="maintaining_level" class="form-control" required placeholder="Enter Maintaining Level">


                    <input type="hidden" name="date_received" value="<?php echo date('Y-m-d'); ?>">

                    <button type="submit" name="add_product" class="btn btn-success mt-3">Add Product</button>
                </form>

            </div>
        </div>
    </div>
</div>

<?php
// Handle product addition
if (isset($_POST['add_product'])) {
    $serial_code = mysqli_real_escape_string($conn, $_POST['serial_code']);
    $lot_no = mysqli_real_escape_string($conn, $_POST['lot_no']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);
    $date_received = mysqli_real_escape_string($conn, $_POST['date_received']);
    $maintaining_level = mysqli_real_escape_string($conn, $_POST['maintaining_level']);

    // Insert into database
    $sql = "INSERT INTO products (serial_code, lot_no, name, price, stock, maintaining_level, date_received, product_status) 
            VALUES ('$serial_code', '$lot_no', '$name', '$price', '$stock', '$maintaining_level', '$date_received', 'Draft')";

    if (mysqli_query($conn, $sql)) {
        echo "<p class='text-success'>Product added successfully!</p>";
    } else {
        echo "<p class='text-danger'>Error adding product: " . mysqli_error($conn) . "</p>";
    }
}
?>


<h2>DRAFT PRODUCTS</h2>
<table class="table">
    <tr>
        <th>ID</th>
        <th>Serial Code</th>
        <th>Lot Number</th>
        <th>Name</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Maintaining Level</th>
        <th>Date Received</th>
        <th>Product Age (Days)</th>
        <th>Action</th>
    </tr>

    <?php

    $sql = "SELECT p.*, DATEDIFF(CURDATE(), p.date_received) AS product_age
            FROM products p
            WHERE p.product_status = 'Draft'
            ";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                <td>" . htmlspecialchars($row["id"]) . "</td>
                <td>" . htmlspecialchars($row["serial_code"]) . "</td>
                <td>" . htmlspecialchars($row["lot_no"]) . "</td>
                <td>" . htmlspecialchars($row["name"]) . "</td>
                <td>₱" . htmlspecialchars($row["price"]) . "</td>
                <td>" . htmlspecialchars($row["stock"]) . "</td>
                <td>" . htmlspecialchars($row["maintaining_level"]) . "</td>
                <td>" . htmlspecialchars($row["date_received"]) . "</td>
                <td>" . htmlspecialchars($row["product_age"]) . "</td>
                <td> <button class='btn btn-primary mt-2'>View</button> </td> <!-- Modal nalang siguro --> 
            </tr>";
        }
    }

    ?>
</table>

<?php include "includes/footer.php"; ?>