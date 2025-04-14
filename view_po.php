<!-- Previously Purchase Order -->
<!-- File name is same parin muna, tsaka nalang palitan tinatamad pa q -->

<title>View Customer Order Slip</title>

<?php
require "includes/conn.php";
$po_id = $_GET['id'];

// Fetch purchase order details with project details
// $orderQuery = "SELECT po.id, po.customer_id, c.name, c.address, po.order_date, po.status, 
//                       p.project_name, p.date_started, p.date_ended
//                FROM purchase_orders po
//                JOIN customers c ON po.customer_id = c.id 
//                JOIN project p ON po.project_id = p.project_id
//                WHERE po.id = $po_id";

$orderQuery = "SELECT po.id, po.customer_id, po.project_id, po.order_date, po.status, po.delivery_address,
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
$items = [];
while ($row = mysqli_fetch_assoc($itemsResult)) {
    $total_price += $row["subtotal"];
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

<h2>Purchase Order Details</h2>
<div id="printable_area">

    <p><strong>Agent:</strong> <?= $order['agent_code'] ?: 'No Agent Code' ?></p>
    <p><strong>Customer:</strong> <?= $order['name'] ?: 'No Customer Name' ?></p>
    <p><strong>Address:</strong> <?= $order['address'] ?: 'No Address' ?></p>
    <p><strong>Delivery Address:</strong> <?= $order['delivery_address'] ?: 'No Delivery Address' ?></p>
    <p><strong>Project Name:</strong> <?= $order['project_name'] ?: 'No Project Name' ?></p>
    <p><strong>Project Date Started:</strong> <?= $order['date_started'] ?: 'No Date Started' ?></p>
    <p><strong>Project Date Ended:</strong> <?= $order['date_ended'] ?: 'No Date Ended' ?></p>
    <p><strong>Purchase Order Date:</strong> <?= $order['order_date'] ?: 'No Order Date' ?></p>
    <p><strong>Purchase Order Status:</strong> <?= $order['status'] ?: 'No Status' ?></p>
    <p><strong>Total Price:</strong> ₱<?= number_format($total_price, 2) ?></p>


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
                <td><?= $row["product_code"] ?></td>
                <td><?= $row["lot_no"] ?></td>
                <td><?= $row["description"] ?></td>
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
<?php if ($order["status"] == "Pending"): ?>
    <form method="post" style="display: inline;">
        <button type="submit" class="btn btn-success">Approve Order</button>
    </form>
<?php elseif ($order["status"] == "Delivery"): ?>
    <p class="text-success"><strong>This order is out for delivery.</strong></p>
<?php elseif ($order["status"] == "Pending Balance"): ?>
    <p class="text-success"><strong>This order has a pending balance.</strong></p>
<?php elseif ($order["status"] == "Completed"): ?>
    <p class="text-success"><strong>This order has already been completed.</strong></p>
<?php else: ?>
    <p class="text-success"><strong>This order has already been approved.</strong></p>
<?php endif; ?>


<a href="view_history.php?id=<?= $order['customer_id'] ?>" class="btn btn-secondary">Back</a>
<a href="view_project.php?id=<?= $order['project_id'] ?>" class="btn btn-info">View Project PO</a>


<!-- Include jsPDF and autoTable -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.21/jspdf.plugin.autotable.min.js"></script>

<script>
    function downloadPDF() {
        const {
            jsPDF
        } = window.jspdf;
        let doc = new jsPDF();

        // Title
        doc.setFont("helvetica", "bold");
        doc.setFontSize(16);
        doc.text("Purchase Order Details", 20, 20);

        // Customer & Order Details
        let y = 30;
        let details = [
            "Agents: <?= $order['agent_code'] ?>",
            "Customer: <?= $order['description'] ?>",
            "Address: <?= $order['address'] ?>",
            "Project Name: <?= $order['project_name'] ?>",
            "Project Date Started: <?= $order['date_started'] ?>",
            "Project Date Ended: <?= $order['date_ended'] ?>",
            "Purchase Order Date: <?= $order['order_date'] ?>",
            "Purchase Order Status: <?= $order['status'] ?>",
            "Total Price: <?= number_format($total_price, 2) ?>"
        ];

        doc.setFont("helvetica", "normal");
        doc.setFontSize(12);
        details.forEach(text => {
            doc.text(text, 20, y);
            y += 10;
        });

        // Table Header
        y += 10;
        doc.setFont("helvetica", "bold");
        doc.text("Order List", 20, y);
        y += 10;

        // Define table columns and rows
        let columns = ["Serial Code", "Lot Number", "Product Name", "Quantity", "Price", "Subtotal"];
        let rows = [
            <?php foreach ($items as $row): ?>["<?= $row['product_code'] ?>", "<?= $row['lot_no'] ?>", "<?= $row['description'] ?>", "<?= $row['quantity'] ?>", "<?= number_format($row['price'], 2) ?>", "<?= number_format($row['subtotal'], 2) ?>"],
            <?php endforeach; ?>
        ];

        // AutoTable for structured table formatting
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

        // Save PDF
        doc.save("Purchase_Order_<?= $po_id ?>.pdf");
    }
</script>

<!-- dapat bawat PO meron unique ID, combination ng Date(YYYY), Name?(not sure) PO_ID siguro, -->

<?php include "includes/footer.php"; ?>