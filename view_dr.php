<title>View Delivery Receipt</title>
<?php

require "includes/conn.php";

$po_id = $_GET['id'];

$sqlDelivery = "SELECT po.id, po.customer_id, po.project_id, po.date_of_cos, po.status, po.delivery_address,
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

$itemsQuery = "SELECT p.id, p.product_code, p.lot_no, p.description, oi.quantity, oi.price, oi.subtotal 
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

        // Update order status to "Completed"
        $updateStatusQuery = "UPDATE purchase_orders SET status = 'Delivery' WHERE id = $po_id";
        mysqli_query($conn, $updateStatusQuery);

        // Refresh the page to reflect the changes
        header("Location: view_dr.php?id=$po_id");
        exit;
    }
}

?>

<h2 class="mb-4">Delivery Receipt</h2>

<div id="printable_area" class="container">

    <!-- Customer & Project Info -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Customer & Project Information</h5>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Agent:</strong> <?= $result['agent_code'] ?: 'No Agent Code' ?></div>
                <div class="col-md-6"><strong>Customer:</strong> <?= $result['name'] ?: 'No Customer Name' ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Address:</strong> <?= $result['address'] ?: 'No Address' ?></div>
                <div class="col-md-6"><strong>Delivery Address:</strong> <?= $result['delivery_address'] ?: 'No Delivery Address' ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Project Name:</strong> <?= $result['project_name'] ?: 'No Project Name' ?></div>
                <div class="col-md-6"><strong>Date of COS:</strong> <?= $result['date_of_cos'] ?: 'No Order Date' ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Project Date Started:</strong> <?= $result['date_started'] ?: 'No Date Started' ?></div>
                <div class="col-md-6"><strong>Project Date Ended:</strong> <?= $result['date_ended'] ?: 'No Date Ended' ?></div>
            </div>
            <div class="row">
                <div class="col-md-6"><strong>Purchase Order Status:</strong> <?= $result['status'] ?: 'No Status' ?></div>
                <div class="col-md-6"><strong>Total Price:</strong> ₱<?= number_format($total_price, 2) ?></div>
            </div>
        </div>
    </div>

    <!-- Product Order List -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Product Order List</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>Serial Code</th>
                            <th>Lot Number</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $row): ?>
                            <tr>
                                <td><?= $row["product_code"] ?></td>
                                <td><?= $row["lot_no"] ?></td>
                                <td><?= $row["description"] ?></td>
                                <td><?= $row["quantity"] ?></td>
                                <td>₱<?= number_format($row["price"], 2) ?></td>
                                <td>₱<?= number_format($row["subtotal"], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mb-4">
        <button class="btn btn-primary" onclick="downloadPDF()">Download PDF</button>

        <?php if ($result["status"] == "Approved"): ?>
            <form method="post" class="d-inline">
                <button type="submit" class="btn btn-success">Deliver Order</button>
            </form>
        <?php elseif ($result["status"] == "Pending"): ?>
            <p class="text-warning d-inline"><strong>This order is not yet approved.</strong></p>
        <?php elseif ($result["status"] == "Delivery"): ?>
            <p class="text-info d-inline"><strong>This order is out for delivery.</strong></p>
        <?php elseif ($result["status"] == "Pending Balance"): ?>
            <p class="text-warning d-inline"><strong>This order has a pending balance.</strong></p>
        <?php elseif ($result["status"] == "Completed"): ?>
            <p class="text-success d-inline"><strong>This order has already been completed.</strong></p>
        <?php else: ?>
            <p class="text-info d-inline"><strong>This order has already been approved.</strong></p>
        <?php endif; ?>

        <a href="delivery_receipt.php" class="btn btn-outline-secondary">← Back</a>
    </div>

</div>

<?php include "includes/footer.php"; ?>

<!-- Broken ang pag download ng pdf -->