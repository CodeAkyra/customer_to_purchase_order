<title>
    Customer Information
</title>

<?php
require "includes/conn.php";

// Fetch customers
$sql = "SELECT id, name, email, address FROM customers";
$result = mysqli_query($conn, $sql);
?>

<h2>CUSTOMER MANAGEMENT</h2>
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

<div class="text-center mt-3">
    <a href="index.php" class="btn btn-primary">Dashboard</a>
    <a href="inventory.php" class="btn btn-primary">Inventory</a>
    <a href="customer_information.php" class="btn btn-primary">Customer Information</a>
    <a href="project.php" class="btn btn-primary"> Project </a>
</div>

<?php

include "includes/footer.php";

?>

<!-- 

- Hindi pa updated yung (customers) sa db
- New table added in db (contact_person)
baka ipaloob ko nalang yung attachments sa (customers) or ewan, hindi ko pa alam ano magandang approach.

meron na ko prototype for uploading files ".pdf", pero iniistore niya locally, mas maganda if yung pdf
is na sstore mismo sa database.

Customer Information:
TIN No:
Customer Name:
Tel. No:
Cellphone No:
Email:
Address:

Contact Person Information:
Contact Person Name:
Tel. No:
Cellphone No.
Email:

Uploads:
Attachments

-->