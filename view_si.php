<title>View Sales Invoice</title>
<?php

require "includes/conn.php";

$po_id = $_GET['id'];

$sqlDelivery = "SELECT po.id, po.customer_id, po.project_id, po.order_date, po.status, po.delivery_address,
                      c.name, c.address, 
                      p.project_name, p.date_started, p.date_ended,
                      a.agent_code
               FROM purchase_orders po
               JOIN customers c ON po.customer_id = c.id 
               LEFT JOIN project p ON po.project_id = p.project_id
               LEFT JOIN agents a ON po.agent_id = a.id
               WHERE po.id = $po_id";

$deliveryResult = mysqli_query($conn, $sqlDelivery);
$result = mysqli_fetch_assoc($deliveryResult);

$itemsQuery = "SELECT p.id, p.serial_code, p.lot_no, p.name, oi.quantity, oi.price, oi.subtotal 
               FROM purchase_order_items oi
               JOIN products p ON oi.product_id = p.id 
               WHERE oi.po_id = $po_id";
$itemsResult = mysqli_query($conn, $itemsQuery);

$total_price = 0;
$items = [];
while ($row = mysqli_fetch_assoc($itemsResult)) {
    $total_price += $row["subtotal"];
    $items[] = $row;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($result["status"] != "Completed") {
        foreach ($items as $item) {
            $product_id = $item["id"];
            $ordered_quantity = $item["quantity"];

            // Deduct stock from inventory
            $updateStockQuery = "UPDATE products SET stock = stock - $ordered_quantity WHERE id = $product_id";
            mysqli_query($conn, $updateStockQuery);
        }

        // Update order status to "Completed"
        $updateStatusQuery = "UPDATE purchase_orders SET status = 'Completed' WHERE id = $po_id";
        // may isa pang ganito, for pending balance
        // sa part na toh, pwede mag complete ng maraming pending balance hanggang sa ma completo yung exact amount na need bayaran ng customer
        // naka base din toh sa credit terms and credit limits, need q pa intindihin unti yung sa part na yun

        mysqli_query($conn, $updateStatusQuery);

        // Refresh the page to reflect the changes
        header("Location: view_si.php?id=$po_id");
        exit;
    }
}

?>


<h2>Sales Invoice</h2>
<div id="printable_area">

    <p><strong>Agent:</strong> <?= $result['agent_code'] ?: 'No Agent Code' ?></p>
    <p><strong>Customer:</strong> <?= $result['name'] ?: 'No Customer Name' ?></p>
    <p><strong>Address:</strong> <?= $result['address'] ?: 'No Address' ?></p>
    <p><strong>Delivery Address:</strong> <?= $result['delivery_address'] ?: 'No Delivery Address' ?></p>
    <p><strong>Project Name:</strong> <?= $result['project_name'] ?: 'No Project Name' ?></p>
    <p><strong>Project Date Started:</strong> <?= $result['date_started'] ?: 'No Date Started' ?></p>
    <p><strong>Project Date Ended:</strong> <?= $result['date_ended'] ?: 'No Date Ended' ?></p>
    <p><strong>Purchase Order Date:</strong> <?= $result['order_date'] ?: 'No Order Date' ?></p>
    <p><strong>Purchase Order Status:</strong> <?= $result['status'] ?: 'No Status' ?></p>
    <p><strong>Total Price:</strong> ₱<?= number_format($total_price, 2) ?></p>

    <h3>Product Order List</h3>
    <table border="1" cellspacing="0" cellpadding="5" width="100%">
        <tr>
            <th>Serial Code</th>
            <th>Lot Number</th>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Subtotal</th>
        </tr>
        <?php foreach ($items as $row): ?>
            <tr>
                <td><?= $row["serial_code"] ?></td>
                <td><?= $row["lot_no"] ?></td>
                <td><?= $row["name"] ?></td>
                <td><?= $row["quantity"] ?></td>
                <td>₱<?= number_format($row["price"], 2) ?></td>
                <td>₱<?= number_format($row["subtotal"], 2) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<!-- Buttons -->
<button class="btn btn-primary" onclick="downloadPDF()">Download PDF</button>

<!-- Complete Order Button (if not yet completed) -->
<?php if ($result["status"] == "Delivery"): ?>
    <form method="post" style="display: inline;">
        <button type="submit" class="btn btn-success">Complete Order</button>
    </form>
<?php elseif ($result["status"] == "Pending"): ?>
    <p class="text-success"><strong>This order is not yet approved.</strong></p>
<?php elseif ($result["status"] == "Approved"): ?>
    <p class="text-success"><strong>This order has already been approved.</strong></p>
<?php elseif ($result["status"] == "Pending Balance"): ?>
    <p class="text-success"><strong>This order has a pending balance.</strong></p>
<?php elseif ($result["status"] == "Completed"): ?>
    <p class="text-success"><strong>This order has already been completed.</strong></p>
<?php else: ?>
    <p class="text-success"><strong>This order has already been approved.</strong></p>
<?php endif; ?>

<?php include "includes/footer.php"; ?>