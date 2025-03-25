<title>Inventory</title>
<?php require "includes/conn.php"; ?>

<h2>Inventory</h2>
<a href="pre_inventory.php" class="btn btn-primary">Pre-Inventory</a> <!-- Tentative pa yung name, naisip ko dito narin yung pag add ng products -->

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
        <th>Date Received</th>
        <th>Product Age (Days)</th>
        <th>Action</th>
    </tr>

    <?php
    $sql = "SELECT p.*, DATEDIFF(CURDATE(), p.date_received) AS product_age, po_i.product_id
    FROM products p
    JOIN purchase_order_items po_i ON p.id = po_i.product_id
    GROUP BY p.id;
    ";
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
                <td>" . htmlspecialchars($row["date_received"]) . "</td>
                <td>" . htmlspecialchars($row["product_age"]) . " days</td>
                <td> <a href='product_transaction.php?id=" . $row["product_id"] . "' class='btn btn-primary mt-2'>View</a> </td> 
                <!-- dapat dito na fefetch yung id sa purchase_order_items -->

            </tr>";
        }
    } else {
        echo "<tr><td colspan='9'>No products found.</td></tr>";
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



<!-- LATEST -->
<!-- dapat kada row ng product, meron button sa gilid, tapos pag pindot, napupunta sa ibang page tapos nakikita lahat ng transaction na binili yung
 product na yun  -->

<!-- Modal siya tapos nafifilter by date, tapos may pagination -->




<!-- Must be able to add new product -->
<!-- Must be able to add quantity to the same product na existing -->
<!-- Must notify user when the product is low or depleting -->
<!-- Implement Batch No. and Serial Code with barcode scanning (gamitin yung scratch it cards as an example) -->


<!-- nakalimutan mag pull woops new learnings -->


<!-- hmmm new problem, pano kung nag order ng same product tapos iaadd sa inventory, hindi naman pwede pag samahin yung two products na mag ka iba na ng takbo yung age ng product -->
<!-- Example: -->

<!-- Existing: Black Paint = (serial_code) 1234567890, (lot_number) 1020304050, (age) 50 days ago, (remaining_stock) 100 -->
<!-- New Arrival: Black Paint = (serial_code) 1234567890, (lot_number) 1020304050, (age) 0 days ago, (arrived) 500 -->

<!-- both are the same, pero bawal pag samahin yung dalawa. Siguro dapat pwede sila mag merge dinamically yung existing quantity, and na sesegregate sila by age. -->
<!-- Mag kakaroon ng revision sa Inventory -->





<!-- MARCH 21, 2025 -->
<!-- dapat kada product meron view all transaction tapos na didisplay lahat ng COS na nacreate na kasama dun yung product na inorder -->