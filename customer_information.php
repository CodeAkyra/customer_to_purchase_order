<title>
    Customer Information
</title>

<?php
require "includes/conn.php";

// Fetch customers
$sql = "SELECT id, name, email, address FROM customers";
$result = mysqli_query($conn, $sql);
?>

<?php

// Fetch customers with their uploaded files
$sql = "SELECT customers.id, customers.name, customers.email, customers.address, uploads.filename 
        FROM customers 
        LEFT JOIN uploads ON customers.id = uploads.customer_id";
$result = mysqli_query($conn, $sql);
?>


<h2>CUSTOMER MANAGEMENT</h2>
<a href="new_customer.php" class="btn btn-success">New Customer</a>

<table class="table table-bordered text-center">

    <th>Name</th>
    <th>Email</th>
    <th>Address</th>
    <th>Actions</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>

            <td><?= $row["name"] ?></td>
            <td><?= $row["email"] ?></td>
            <td><?= $row["address"] ?></td>
            <td>
                <a href="edit_customer.php?id=<?= $row["id"] ?>" class="btn btn-warning">Edit</a>
                <a href="view_history.php?id=<?= $row["id"] ?>" class="btn btn-info">View History</a>
                <a href="create_po.php?customer_id=<?= $row["id"] ?>" class="btn btn-primary">Create PO</a>

                <!-- View PDF Button (Only if file exists) -->
                <?php if (!empty($row['filename'])): ?>
                    <button class="btn btn-secondary" onclick="openPDF('includes/uploads/<?= $row["filename"] ?>')">View PDF</button>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<!-- Bootstrap Modal for PDF Viewer -->
<div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">PDF Viewer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="pdfFrame" src="" width="100%" height="500px"></iframe>
            </div>
        </div>
    </div>
</div>

<div class="text-center mt-3">
    <a href="index.php" class="btn btn-primary">Dashboard</a>
    <a href="inventory.php" class="btn btn-primary">Inventory</a>
    <a href="customer_information.php" class="btn btn-primary">Customer Information</a>
    <a href="project.php" class="btn btn-primary"> Project </a>
</div>

<script>
    function openPDF(fileUrl) {
        document.getElementById('pdfFrame').src = fileUrl;
        var pdfModal = new bootstrap.Modal(document.getElementById('pdfModal'));
        pdfModal.show();
    }
</script>

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