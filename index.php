<title>
    Customer Information
</title>

<?php
require "includes/conn.php";

// Fetch customers
$sql = "SELECT id, name, email, address FROM customers";
$result = mysqli_query($conn, $sql);
?>

<h2>Customer Management</h2>
<a href="new_customer.php" class="btn btn-success">New Customer</a>

<table class="table">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Address</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $row["id"] ?></td>
            <td><?= $row["name"] ?></td>
            <td><?= $row["email"] ?></td>
            <td><?= $row["address"] ?></td>
            <td>
                <a href="edit_customer.php?id=<?= $row["id"] ?>" class="btn btn-warning">Edit</a>
                <a href="view_history.php?id=<?= $row["id"] ?>" class="btn btn-info">View History</a>
                <a href="create_po.php?customer_id=<?= $row["id"] ?>" class="btn btn-primary">Create PO</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<a href="inventory.php" class="btn btn-primary"> Inventory </a>
<a href="dashboard.php" class="btn btn-primary"> Dashboard </a>

<?php

include "includes/footer.php";

?>