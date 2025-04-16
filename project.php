<title>Projects</title>
<?php
require "includes/conn.php";

// Fetch all projects along with their associated customer names and total price
$sql = "SELECT p.project_id, p.project_name, p.date_started, p.date_ended, c.name AS customer_name,
               COALESCE(SUM(po_items.subtotal), 0) AS total_price
        FROM project p
        JOIN customers c ON p.customer_id = c.id
        LEFT JOIN purchase_orders po ON po.project_id = p.project_id
        LEFT JOIN purchase_order_items po_items ON po.id = po_items.po_id
        GROUP BY p.project_id";

$result = mysqli_query($conn, $sql);
?>
<h2 class="text-center mb-4">Projects</h2>

<table class="table">
    <tr>
        <th>Project ID</th>
        <th>Project Name</th>
        <th>Date Started</th>
        <th>Date Ended</th>
        <th>Customer Name</th>
        <th>Total Price</th>
        <th>Action</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $row["project_id"] ?></td>
            <td><?= $row["project_name"] ?></td>
            <td><?= $row["date_started"] ?></td>
            <td><?= $row["date_ended"] ?: 'Ongoing' ?></td>
            <td><?= $row["customer_name"] ?></td>
            <td>₱<?= number_format($row["total_price"], 2) ?></td>
            <td>
                <a href="view_project.php?id=<?= $row['project_id'] ?>" class="btn btn-info">View</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php include "includes/footer.php"; ?>



<!-- Project -->

<!-- Nakakapag create ng project -->
<!-- Pag nag create ng project, naka indicate agad kung kelan nag start ang project -->
<!-- Na seselect yung project sa create_po.php -->
<!-- Basta yung approach niya is parang same siya ng concept kay view_history, and ganto siya view_history.php > projects.php > view_po.php -->
<!-- Nakikita kung magkano total expenses sa project na toh -->
<!-- May option siguro iclose and open ang project, idk -->