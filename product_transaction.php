<title>Product Transaction</title>

<?php
require "includes/conn.php";

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $_GET['id'];

    $poIQuery = "SELECT po_i.product_id, po_i.id, po_i.po_id, po_i.quantity, po_i.price, po_i.subtotal, po.date_created, p.name, c.name as customer_name
    FROM purchase_order_items po_i
    LEFT JOIN purchase_orders po ON po_i.po_id = po.id
    LEFT JOIN customers c ON po.customer_id = c.id
    LEFT JOIN products p ON po_i.product_id = p.id
    WHERE po_i.product_id = $product_id";

    $poIResult = mysqli_query($conn, $poIQuery);

    if (mysqli_num_rows($poIResult) > 0) {
        $fetchName = mysqli_fetch_array($poIResult);
        $productName = htmlspecialchars($fetchName['name']);
    } else {
        $productName = "No Transactions Yet";
    }
} else {
    echo "<div class='alert alert-danger'>No transactions found</div>";
    exit;
}
?>

<div>
    <h1>Purchase Records for - <?php echo $productName; ?></h1> <!-- tentative, pero baka eto gamitin yung name para sa module na toh or page na toh -->
    <a href="inventory.php" class="btn btn-primary">Return</a>
</div>

<table class="table">
    <tr>
        <th>Product Name</th>
        <th>Customer Name</th>
        <th>COS Date Created</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Subtotal</th>
        <th>Action</th>
    </tr>

    <?php
    if (isset($poIResult) && mysqli_num_rows($poIResult) > 0) {
        mysqli_data_seek($poIResult, 0);
        while ($row = mysqli_fetch_array($poIResult)) {
            echo "<tr>
                <td>" . htmlspecialchars($row["name"]) . "</td>
                <td>" . htmlspecialchars($row["customer_name"]) . "</td>
                <td>" . htmlspecialchars($row["date_created"]) . "</td>
                <td>" . htmlspecialchars($row["quantity"]) . "</td>
                <td>" . htmlspecialchars($row["price"]) . "</td>
                <td>" . htmlspecialchars($row["subtotal"]) . "</td>
                <td>
                    <a href='view_po.php?id=" . $row['po_id'] . "' class='btn btn-outline-dark'>View</a>
                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='7' class='text-center'>No transactions found</td></tr>";
    }
    ?>
</table>