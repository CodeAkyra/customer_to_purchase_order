<title>
    View Purchase Order
</title>

<?php
include "includes/conn.php";
$po_id = $_GET['id'];

// Fetch purchase order details
$orderQuery = "SELECT po.id, po.customer_id, c.name, c.address, po.order_date, po.status 
               FROM purchase_orders po
               JOIN customers c ON po.customer_id = c.id 
               WHERE po.id = $po_id";

$orderResult = mysqli_query($conn, $orderQuery);
$order = mysqli_fetch_assoc($orderResult);

// Fetch order items
$itemsQuery = "SELECT p.id, p.serial_code, p.lot_no, p.name, oi.quantity, oi.price, oi.subtotal 
               FROM purchase_order_items oi
               JOIN products p ON oi.product_id = p.id 
               WHERE oi.po_id = $po_id";
$itemsResult = mysqli_query($conn, $itemsQuery);

// Initialize total price
$total_price = 0;
$items = [];
while ($row = mysqli_fetch_assoc($itemsResult)) {
    $total_price += $row["subtotal"];
    $items[] = $row;
}

// Handle "Complete Order" action
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($order["status"] != "Completed") {
        foreach ($items as $item) {
            $product_id = $item["id"];
            $ordered_quantity = $item["quantity"];

            // Deduct stock from inventory
            $updateStockQuery = "UPDATE products SET stock = stock - $ordered_quantity WHERE id = $product_id";
            mysqli_query($conn, $updateStockQuery);
        }

        // Update order status to "Completed"
        $updateStatusQuery = "UPDATE purchase_orders SET status = 'Completed' WHERE id = $po_id";
        mysqli_query($conn, $updateStatusQuery);

        // Refresh the page to reflect the changes
        header("Location: view_po.php?id=$po_id");
        exit;
    }
}
?>

<h2>Purchase Order Details</h2>
<p><strong>Customer:</strong> <?= $order['name'] ?></p>
<p><strong>Address:</strong> <?= $order['address'] ?></p>
<p><strong>Date:</strong> <?= $order['order_date'] ?></p>
<p><strong>Status:</strong> <?= $order['status'] ?></p>
<p><strong>Total Price:</strong> <?= number_format($total_price, 2) ?></p>

<h3>Products</h3>
<table class="table">
    <tr>
        <th>Serial Code</th>
        <th>Lot Number</th>
        <th>Product Name</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Subtotal</th>
    </tr>
    <?php if (!empty($items)): ?>
        <?php foreach ($items as $row): ?>
            <tr>
                <td><?= $row["serial_code"] ?></td>
                <td><?= $row["lot_no"] ?></td>
                <td><?= $row["name"] ?></td>
                <td><?= $row["quantity"] ?></td>
                <td><?= number_format($row["price"], 2) ?></td>
                <td><?= number_format($row["subtotal"], 2) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="4" class="text-center">No products found for this order.</td>
        </tr>
    <?php endif; ?>
</table>

<!-- Show Complete Order button only if the order is not yet completed -->
<?php if ($order["status"] != "Completed"): ?>
    <form method="post">
        <button type="submit" class="btn btn-success">Complete Order</button>
    </form>
<?php else: ?>
    <p class="text-success"><strong>This order has already been completed.</strong></p>
<?php endif; ?>

<a href="view_history.php?id=<?= $order['customer_id'] ?>" class="btn btn-secondary">Back</a>


<?php

include "includes/footer.php";

?>