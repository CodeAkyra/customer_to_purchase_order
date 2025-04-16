<!-- Previously Purchase Order -->
<!-- File name is same parin muna, tsaka nalang palitan tinatamad pa q -->

<title>View Customer Order Slip</title>

<?php
require "includes/conn.php";
$po_id = $_GET['id'];

// Fetch purchase order details with project details
// $orderQuery = "SELECT po.id, po.customer_id, c.name, c.address, po.date_of_cos, po.status, 
//                       p.project_name, p.date_started, p.date_ended
//                FROM purchase_orders po
//                JOIN customers c ON po.customer_id = c.id 
//                JOIN project p ON po.project_id = p.project_id
//                WHERE po.id = $po_id";

$orderQuery = "SELECT po.id, po.segment, po.sub_segment, po.customer_id, po.project_id, po.date_of_cos, po.status, po.delivery_address,
                      c.name, c.address, 
                      p.project_name, p.date_started, p.date_ended,
                      a.agent_code
               FROM purchase_orders po
               JOIN customers c ON po.customer_id = c.id 
               LEFT JOIN project p ON po.project_id = p.project_id
               LEFT JOIN agents a ON po.agent_id = a.id
               WHERE po.id = $po_id";
// para bumalik sa project

$orderResult = mysqli_query($conn, $orderQuery);
$order = mysqli_fetch_assoc($orderResult);

// Fetch order items
$itemsQuery = "SELECT p.id, p.product_code, p.lot_no, p.description, oi.quantity, oi.price, oi.subtotal 
               FROM purchase_order_items oi
               JOIN products p ON oi.product_id = p.id 
               WHERE oi.po_id = $po_id";
$itemsResult = mysqli_query($conn, $itemsQuery);

// Initialize total price
$total_price = 0;
$total_quantity = 0;
$items = [];
while ($row = mysqli_fetch_assoc($itemsResult)) {
    $total_price += $row["subtotal"];
    $total_quantity += $row["quantity"];
    $items[] = $row;
}

// Handle "Complete Order" action
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($order["status"] != "Completed") {
        // Update order status to "Completed"
        $updateStatusQuery = "UPDATE purchase_orders SET status = 'Approved' WHERE id = $po_id";
        mysqli_query($conn, $updateStatusQuery);

        // Refresh the page to reflect the changes
        header("Location: view_po.php?id=$po_id");
        exit;
    }
}
?>

<h2 class="mb-4">Customer Order Slip Details</h2>

<div id="printable_area" class="container">

    <?php
    $null = "<strong style='color: red;'> NULL </strong>";
    // ayusin ko pa yung mga naka null, dapat may data na yan. try ko ma tapos lahat by today or until tomorrow
    ?>

    <!-- Customer & Project Info -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Customer & Project Information</h5>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Customer:</strong> <?= $order['name'] ?: 'No Customer Name' ?></div>
                <div class="col-md-6"><strong>COS Number:</strong><?php echo $null ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Address:</strong> <?= $order['address'] ?: 'No Address' ?></div>
                <div class="col-md-6"><strong>Date of COS:</strong> <?= $order['date_of_cos'] ?: 'No Order Date' ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Delivery Address:</strong> <?= $order['delivery_address'] ?: 'No Delivery Address' ?></div>
                <div class="col-md-6"><strong>Terms:</strong><?php echo $null ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Project Name:</strong> <?= $order['project_name'] ?: 'No Project Name' ?></div>
                <div class="col-md-6"><strong>Credit Limit:</strong><?php echo $null ?></div>
            </div>
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6"><strong>PO No.:</strong><?php echo $null ?></div>
            </div>
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6"><strong>Ordered By:</strong><?php echo $null ?></div>
            </div>
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6"><strong>TSR:</strong> <?= $order['agent_code'] ?: 'No Agent Code' ?></div>
            </div>
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6"><strong>Segment:</strong> <?= $order['segment'] ?: 'No Agent Code' ?></div>
            </div>
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6"><strong>Subsegment:</strong> <?= $order['sub_segment'] ?: 'No Agent Code' ?></div>
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

        <?php if ($order["status"] == "Pending"): ?>
            <form method="post" class="d-inline">
                <button type="submit" class="btn btn-success">Approve Order</button>
            </form>
        <?php elseif ($order["status"] == "Delivery"): ?>
            <p class="text-success d-inline"><strong>This order is out for delivery.</strong></p>
        <?php elseif ($order["status"] == "Pending Balance"): ?>
            <p class="text-warning d-inline"><strong>This order has a pending balance.</strong></p>
        <?php elseif ($order["status"] == "Completed"): ?>
            <p class="text-success d-inline"><strong>This order has already been completed.</strong></p>
        <?php else: ?>
            <p class="text-info d-inline"><strong>This order has already been approved.</strong></p>
        <?php endif; ?>

        <a href="view_history.php?id=<?= $order['customer_id'] ?>" class="btn btn-outline-secondary">← Back</a>
        <a href="view_project.php?id=<?= $order['project_id'] ?>" class="btn btn-info">View Project PO</a>
    </div>

</div>


<!-- Include jsPDF and autoTable -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.21/jspdf.plugin.autotable.min.js"></script>

<script>
    function downloadPDF() {
        const {
            jsPDF
        } = window.jspdf;
        const doc = new jsPDF();

        // Title
        doc.setFont("helvetica", "bold");
        doc.setFontSize(16);
        doc.text("Customer Order Slip Details", 20, 20);

        // Customer & Order Details
        let y = 30;
        let details = [
            "Customer: <?= $order['name'] ?: 'No Customer Name' ?>",
            "COS Number: NULL",
            "Address: <?= $order['address'] ?: 'No Address' ?>",
            "Date of COS: <?= $order['date_of_cos'] ?: 'No Order Date' ?>",
            "Delivery Address: <?= $order['delivery_address'] ?: 'No Delivery Address' ?>",
            "Terms: NULL",
            "Project Name: <?= $order['project_name'] ?: 'No Project Name' ?>",
            "Credit Limit: NULL",
            "PO No.: NULL",
            "Ordered By: NULL",
            "TSR: <?= $order['agent_code'] ?: 'No Agent Code' ?>",
            "Segment: <?= $order['segment'] ?: 'No Segment' ?>",
            "Subsegment: <?= $order['sub_segment'] ?: 'No Subsegment' ?>",
            "VAT: <?= $order['vat'] ?: 'No VAT' ?>"
        ];

        // Add details to the PDF
        doc.setFont("helvetica", "normal");
        doc.setFontSize(12);
        details.forEach((text, index) => {
            doc.text(text, 20, y + (index * 10));
        });

        // Table Header
        y += details.length * 10 + 10;
        doc.setFont("helvetica", "bold");
        doc.text("Product Order List", 20, y);
        y += 10;

        // Define table columns and rows
        const columns = ["Quantity", "Unit", "Item / Code", "Description", "Price / Liter", "Net Selling Price"];
        const rows = [
            <?php foreach ($items as $row): ?>["<?= $row['quantity'] ?>", "Liters", "<?= $row['product_code'] ?>", "<?= $row['description'] ?>", "₱<?= number_format($row['price'], 2) ?>", "₱<?= number_format($row['subtotal'], 2) ?>"],
            <?php endforeach; ?>
        ];

        // Use AutoTable for structured table formatting
        doc.autoTable({
            startY: y,
            head: [columns],
            body: rows,
            theme: 'grid',
            styles: {
                fontSize: 10
            },
            headStyles: {
                fillColor: [100, 100, 100]
            }
        });

        // Add total price
        y = doc.lastAutoTable.finalY + 10; // Position after the table
        doc.setFont("helvetica", "bold");
        doc.text("Total Price: ₱<?= number_format($total_price, 2) ?>", 20, y);

        // Save PDF
        doc.save("Purchase_Order_<?= $order['customer_id'] ?>.pdf");
    }
</script>
<!-- dapat bawat PO meron unique ID, combination ng Date(YYYY), Name?(not sure) PO_ID siguro, -->

<?php include "includes/footer.php"; ?>



<!-- 
MGA IDADAGDAG
<p><strong>Deliver To:</strong>NULL</p>
<p><strong>COS Number:</strong>NULL</p>
<p><strong>Date:</strong>NULL</p>
<p><strong>Terms:</strong>NULL</p>
<p><strong>Credit Limit:</strong>NULL</p>
<p><strong>Po No:</strong>NULL</p>
<p><strong>Ordered By:</strong>NULL</p>
<p><strong>TSR:</strong>NULL</p>
<p><strong>Segment:</strong>NULL</p>
<p><strong>Subsegment:</strong>NULL</p>
<p><strong>VAT:</strong>NULL</p>
-->