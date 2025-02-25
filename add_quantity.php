<title>Add Quantity</title>

<?php
require "includes/conn.php";

// Get serial_code and lot_no from GET request
$serial_code = $_GET["serial_code"] ?? null;
$lotNumber = $_GET["lot_no"] ?? null;

// Sanitize input (prevent SQL injection)
$serial_code = mysqli_real_escape_string($conn, $serial_code);
$lotNumber = mysqli_real_escape_string($conn, $lotNumber);

// Fetch product using serial_code or lot_no
$product_query = "SELECT * FROM products WHERE serial_code = '$serial_code' OR lot_no = '$lotNumber'";
$product_result = mysqli_query($conn, $product_query);
$product = mysqli_fetch_assoc($product_result);

if (!$product) {
    die("Product not found!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stock_to_add = intval($_POST["stock_to_add"]); // Ensure input is an integer

    if ($stock_to_add > 0) {
        $identifier = !empty($serial_code) ? "serial_code = '$serial_code'" : "lot_no = '$lotNumber'";
        $update_query = "UPDATE products SET stock = stock + $stock_to_add WHERE $identifier";
        mysqli_query($conn, $update_query);

        echo "<script>alert('Stock successfully added!'); window.location.href='inventory.php';</script>";
        exit();
    } else {
        echo "<script>alert('Please enter a valid stock quantity.');</script>";
    }
}
?>

<h2>Add Stock</h2>

<table class="table">
    <tr>
        <th>ID</th>
        <th>Serial Code</th>
        <th>Lot Number</th>
        <th>Name</th>
        <th>Price</th>
        <th>Current Stock</th>
    </tr>
    <tr>
        <td><?= htmlspecialchars($product['id']) ?></td>
        <td><?= htmlspecialchars($product['serial_code']) ?></td>
        <td><?= htmlspecialchars($product['lot_no']) ?></td>
        <td><?= htmlspecialchars($product['name']) ?></td>
        <td><?= htmlspecialchars($product['price']) ?></td>
        <td><?= htmlspecialchars($product['stock']) ?></td>
    </tr>
</table>

<form method="post" onsubmit="return confirmStockAddition()">
    <label for="stock_to_add">Stock to Add:</label>
    <input type="number" id="stock_to_add" name="stock_to_add" min="1" required>
    <button type="submit" class="btn btn-primary">Add Quantity</button>
    <a href="inventory.php" class="btn btn-secondary">Back</a>
</form>

<script>
    function confirmStockAddition() {
        let stockValue = document.getElementById('stock_to_add').value;
        if (stockValue.trim() === "" || parseInt(stockValue) <= 0) {
            alert("Please enter a valid stock quantity.");
            return false;
        }
        return confirm("Are you sure you want to add " + stockValue + " to the stock?");
    }
</script>