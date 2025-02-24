<?php
include "conn.php";
$customer_id = $_GET['customer_id'];

// Fetch customer details
$customerQuery = "SELECT * FROM customers WHERE id = $customer_id";
$customerResult = mysqli_query($conn, $customerQuery);
$customer = mysqli_fetch_assoc($customerResult);

// Fetch available products
$productQuery = "SELECT * FROM products";
$productResult = mysqli_query($conn, $productQuery);

// Handle PO Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = date("Y-m-d H:i:s");
    mysqli_query($conn, "INSERT INTO purchase_orders (customer_id, order_date, status) VALUES ($customer_id, '$date', 'Pending')");
    $po_id = mysqli_insert_id($conn);

    foreach ($_POST['products'] as $product_id => $quantity) {
        if ($quantity > 0) { // Ensure only selected products are added
            $priceQuery = mysqli_query($conn, "SELECT price FROM products WHERE id = $product_id");
            $price = mysqli_fetch_assoc($priceQuery)['price'];
            $subtotal = $price * $quantity;

            mysqli_query($conn, "INSERT INTO purchase_order_items (po_id, product_id, quantity, price, subtotal) 
                                 VALUES ($po_id, $product_id, $quantity, $price, $subtotal)");
        }
    }
    header("Location: view_po.php?id=$po_id");
    exit();
}
?>

<h2>Create Purchase Order</h2>
<p><strong>Customer:</strong> <?= $customer['name'] ?></p>
<p><strong>Address:</strong> <?= $customer['address'] ?></p>

<form method="post">
    <h3>Select Products</h3>
    <table class="table">
        <tr>
            <th>Product</th>
            <th>Available Stock</th>
            <th>Quantity</th>
        </tr>
        <?php while ($product = mysqli_fetch_assoc($productResult)): ?>
            <tr>
                <td><?= $product['name'] ?></td>
                <td><?= $product['stock'] ?></td>
                <td>
                    <input type="number" name="products[<?= $product['id'] ?>]" min="0" max="<?= $product['stock'] ?>" value="0">
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <button type="submit" class="btn btn-success">Submit PO</button>
</form>

<a href="index.php" class="btn btn-secondary">Back</a>