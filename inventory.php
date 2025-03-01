<title>Inventory</title>
<?php require "includes/conn.php"; ?>

<h2>Inventory</h2>

<!-- Add Product Button (Opens Modal) -->
<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
    + Add Product
</button>

<!-- Add Product Modal -->

<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
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
    $maintaining_level = mysqli_real_escape_string($conn, $_POST['maintaining_level']);

    // Insert into database
    $sql = "INSERT INTO products (serial_code, lot_no, name, price, stock, maintaining_level) 
            VALUES ('$serial_code', '$lot_no', '$name', '$price', '$stock', '$maintaining_level')";

    if (mysqli_query($conn, $sql)) {
        echo "<p class='text-success'>Product added successfully!</p>";
    } else {
        echo "<p class='text-danger'>Error adding product: " . mysqli_error($conn) . "</p>";
    }
}
?>

<!-- Search Product -->
<form action="" method="get">
    <h3>SEARCH PRODUCT BY SERIAL CODE OR LOT NUMBER</h3>
    <label>Enter Serial Code</label>
    <input type="text" name="serialCode" placeholder="Enter Here" class="form-control">

    <label>Enter Lot Number</label>
    <input type="text" name="lotNumber" placeholder="Enter Here" class="form-control">

    <button type="submit" class="btn btn-primary mt-2">Search</button>
</form>

<?php
if (!empty($_GET['serialCode']) || !empty($_GET['lotNumber'])) {
    echo "<table class='table'>";
    echo "<tr>
            <th>ID</th>
            <th>Serial Code</th>
            <th>Lot Number</th>
            <th>Name</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Maintaining Level</th>
            <th>Action</th>
        </tr>";

    $found = false;

    if (!empty($_GET['serialCode'])) {
        $serial_code = mysqli_real_escape_string($conn, $_GET['serialCode']);
        $sql = "SELECT * FROM products WHERE serial_code = '$serial_code'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $found = true;
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>" . htmlspecialchars($row["id"]) . "</td>
                        <td>" . htmlspecialchars($row["serial_code"]) . "</td>
                        <td>" . htmlspecialchars($row["lot_no"]) . "</td>
                        <td>" . htmlspecialchars($row["name"]) . "</td>
                        <td>" . htmlspecialchars($row["price"]) . "</td>
                        <td>" . htmlspecialchars($row["stock"]) . "</td>
                        <td>" . htmlspecialchars($row["maintaining_level"]) . "</td>
                        <td>
                            <a href='add_quantity.php?serial_code=" . urlencode($row["serial_code"]) . "' class='btn btn-primary'>Edit</a>
                        </td>
                    </tr>";
            }
        }
    }

    if (!empty($_GET['lotNumber'])) {
        $lot_no = mysqli_real_escape_string($conn, $_GET['lotNumber']);
        $sql = "SELECT * FROM products WHERE lot_no = '$lot_no'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $found = true;
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>" . htmlspecialchars($row["id"]) . "</td>
                        <td>" . htmlspecialchars($row["serial_code"]) . "</td>
                        <td>" . htmlspecialchars($row["lot_no"]) . "</td>
                        <td>" . htmlspecialchars($row["name"]) . "</td>
                        <td>" . htmlspecialchars($row["price"]) . "</td>
                        <td>" . htmlspecialchars($row["stock"]) . "</td>
                        <td>" . htmlspecialchars($row["maintaining_level"]) . "</td>
                        <td>
                            <a href='add_quantity.php?lot_no=" . urlencode($row["lot_no"]) . "' class='btn btn-primary'>Edit</a>
                        </td>
                    </tr>";
            }
        }
    }

    if (!$found) {
        echo "<tr><td colspan='8'>No products found.</td></tr>";
    }

    echo "</table>";
}
?>

<h2>INVENTORY TABLE</h2>

<!-- Inventory Table -->
<table class='table'>
    <tr>
        <th>ID</th>
        <th>Serial Code</th>
        <th>Lot Number</th>
        <th>Name</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Maintaining Level</th>
    </tr>

    <?php
    $sql = "SELECT * FROM products";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                <td>" . htmlspecialchars($row["id"]) . "</td>
                <td>" . htmlspecialchars($row["serial_code"]) . "</td>
                <td>" . htmlspecialchars($row["lot_no"]) . "</td>
                <td>" . htmlspecialchars($row["name"]) . "</td>
                <td>" . htmlspecialchars($row["price"]) . "</td>
                <td>" . htmlspecialchars($row["stock"]) . "</td>
                <td>" . htmlspecialchars($row["maintaining_level"]) . "</td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No products found.</td></tr>";
    }
    ?>
</table>

<div class="text-center mt-3">
    <a href="index.php" class="btn btn-primary">Dashboard</a>
    <a href="inventory.php" class="btn btn-primary">Inventory</a>
    <a href="customer_information.php" class="btn btn-primary">Customer Information</a>
    <a href="project.php" class="btn btn-primary"> Project </a>
</div>

<?php include "includes/footer.php"; ?>



<!-- Must be able to add new product -->
<!-- Must be able to add quantity to the same product na existing -->
<!-- Must notify user when the product is low or depleting -->
<!-- Implement Batch No. and Serial Code with barcode scanning (gamitin yung scratch it cards as an example) -->


<!-- nakalimutan mag pull woops new learnings -->