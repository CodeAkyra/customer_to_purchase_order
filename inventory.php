<title>Inventory</title>

<?php include "includes/conn.php"; ?>

<h2>Inventory</h2>

<?php
$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);

echo "<table class='table'>";
echo "<tr>
        <th>ID</th>
        <th>Serial Code</th>
        <th>Lot Number</th>
        <th>Name</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Maintaining Level</th>
    </tr>";

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
    echo "<tr><td colspan='6'>No products found.</td></tr>";
}
echo "</table>";
?>

<a href="index.php" class="btn btn-primary">Customer Information</a>
<a href="dashboard.php" class="btn btn-primary"> Dashboard </a>

<!-- Search Product -->
<form action="" method="get">
    <h2>SEARCH PRODUCT BY SERIAL CODE OR LOT NUMBER</h2>
    <label>Enter Serial Code</label>
    <input type="text" name="serialCode" placeholder="Enter Here">

    <label>Enter Lot Number</label>
    <input type="text" name="lotNumber" placeholder="Enter Here">

    <button type="submit" class="btn btn-primary">Search</button>
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
        echo "<tr><td colspan='7'>No products found.</td></tr>";
    }

    echo "</table>";
}
?>

<?php include "includes/footer.php"; ?>


<!-- Must be able to add new product -->
<!-- Must be able to add quantity to the same product na existing -->
<!-- Must notify user when the product is low or depleting -->
<!-- Implement Batch No. and Serial Code with barcode scanning (gamitin yung scratch it cards as an example) -->


<!-- nakalimutan mag pull woops new learnings -->