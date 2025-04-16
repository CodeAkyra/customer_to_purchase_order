<title>View Sales Invoice</title>
<?php

require "includes/conn.php";

$po_id = $_GET['id'];

$sqlDelivery = "SELECT po.id, po.segment, po.sub_segment, po.customer_id, po.project_id, po.date_of_cos, po.status, po.delivery_address,
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
$total_quantity = 0;
$items = [];
while ($row = mysqli_fetch_assoc($itemsResult)) {
    $total_price += $row["subtotal"];
    $total_quantity += $row["quantity"];
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

<h2 class="text-center mb-4">Sales Invoice</h2>


<div id="printable_area" class="container">

    <?php
    $null = "<strong style='color: red;'> NULL </strong>";
    // ayusin ko pa yung mga naka null, dapat may data na yan. try ko ma tapos lahat by today or until tomorrow
    ?>

    <!-- Customer & Project Info -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-6"><strong>Customer:</strong> <?= $result['name'] ?: 'No Customer Name' ?></div>
                <div class="col-md-6"><strong>COS Number:</strong><?php echo $null ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Address:</strong> <?= $result['address'] ?: 'No Address' ?></div>
                <div class="col-md-6"><strong>Date of COS:</strong> <?= $result['date_of_cos'] ?: 'No Order Date' ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Delivery Address:</strong> <?= $result['delivery_address'] ?: 'No Delivery Address' ?></div>
                <div class="col-md-6"><strong>Terms:</strong><?php echo $null ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Project Name:</strong> <?= $result['project_name'] ?: 'No Project Name' ?></div>
                <div class="col-md-6"><strong>Credit Limit:</strong><?php echo $null ?></div>
            </div>
            <div class="row">
                <div class="col-md-6"><strong>Status:</strong> <?= $result['status'] ?: 'No Project Name' ?></div>
                <div class="col-md-6"><strong>PO No.:</strong><?php echo $null ?></div>
            </div>
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6"><strong>Ordered By:</strong><?php echo $null ?></div>
            </div>
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6"><strong>TSR:</strong> <?= $result['agent_code'] ?: 'No Agent Code' ?></div>
            </div>
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6"><strong>Segment:</strong> <?= $result['segment'] ?: 'No Agent Code' ?></div>
            </div>
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6"><strong>Subsegment:</strong> <?= $result['sub_segment'] ?: 'No Agent Code' ?></div>
            </div>
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6"><strong>VAT:</strong><?php echo $null ?></div>
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
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Item / Code</th>
                            <th>Description</th>
                            <th>Price / Liter</th>
                            <!-- <th>Disc. (Discount)</th> ewan ko pa toh -->
                            <th>Net Selling Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $row): ?>
                            <tr>
                                <td><?= $row["quantity"] ?></td>
                                <td> Liters </td>
                                <td><?= $row["product_code"] ?></td>
                                <td><?= $row["description"] ?></td>
                                <td>₱<?= number_format($row["price"], 2) ?></td>
                                <td>₱<?= number_format($row["subtotal"], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="col-md-6"><strong>Total Quantity:</strong> <?= number_format($total_quantity) ?></div>
                <div class="col-md-6"><strong>Total Price:</strong> ₱<?= number_format($total_price, 2) ?></div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mb-4">
        <button class="btn btn-primary" onclick="downloadPDF()">Download PDF</button>

        <?php if ($result["status"] == "Delivery"): ?>
            <form method="post" class="d-inline">
                <button type="submit" class="btn btn-success">Complete Order</button>
            </form>
        <?php elseif ($result["status"] == "Pending"): ?>
            <p class="text-warning d-inline"><strong>This order is not yet approved.</strong></p>
        <?php elseif ($result["status"] == "Approved"): ?>
            <p class="text-info d-inline"><strong>This order has already been approved.</strong></p>
        <?php elseif ($result["status"] == "Pending Balance"): ?>
            <p class="text-warning d-inline"><strong>This order has a pending balance.</strong></p>
        <?php elseif ($result["status"] == "Completed"): ?>
            <p class="text-success d-inline"><strong>This order has already been completed.</strong></p>
        <?php else: ?>
            <p class="text-info d-inline"><strong>This order has already been approved.</strong></p>
        <?php endif; ?>

        <a href="sales_invoice.php" class="btn btn-outline-secondary">← Back</a>
    </div>

</div>




<?php include "includes/footer.php"; ?>

<!-- Broken ang pag download ng pdf -->