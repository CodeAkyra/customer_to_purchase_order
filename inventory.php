<title>Inventory</title>
<?php require "includes/conn.php"; ?>

<h2>Inventory</h2>
<a href="pre_inventory.php" class="btn btn-primary">Pre-Inventory</a> <!-- Tentative pa yung name, naisip ko dito narin yung pag add ng products -->
<!-- bukas ko nalang asikasuhin toh, tatanggalin ko na yung pre_inventory then ayusin q yung po_products.php -->

<!-- Search Product -->
<form action="" method="get">
    <h3>SEARCH PRODUCT BY SERIAL CODE OR LOT NUMBER</h3>
    <label>Enter Serial Code</label>
    <input type="text" name="serialCode" placeholder="Enter Here" class="form-control">

    <label>Enter Lot Number</label>
    <input type="text" name="lotNumber" placeholder="Enter Here" class="form-control">

    <button type="submit" class="btn btn-primary mt-2">Search</button>
</form>

<?php
if (!empty($_GET['serialCode']) || !empty($_GET['lotNumber'])) {
    echo "<table class='table'>";
    echo "<tr>
            <th>ID</th>
            <th>Serial Code</th>
            <th>Lot Number</th>
            <th>Name</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Maintaining Level</th>
            <th>Action</th>
        </tr>";

    $found = false;

    if (!empty($_GET['serialCode'])) {
        $serial_code = mysqli_real_escape_string($conn, $_GET['serialCode']);
        $sql = "SELECT * FROM products WHERE serial_code = '$serial_code'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $found = true;
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>" . htmlspecialchars($row["id"]) . "</td>
                        <td>" . htmlspecialchars($row["serial_code"]) . "</td>
                        <td>" . htmlspecialchars($row["lot_no"]) . "</td>
                        <td>" . htmlspecialchars($row["name"]) . "</td>
                        <td>" . htmlspecialchars($row["price"]) . "</td>
                        <td>" . htmlspecialchars($row["stock"]) . "</td>
                        <td>" . htmlspecialchars($row["maintaining_level"]) . "</td>
                        <td>
                            <a href='add_quantity.php?serial_code=" . urlencode($row["serial_code"]) . "' class='btn btn-primary'>Edit</a>
                        </td>
                    </tr>";
            }
        }
    }

    if (!empty($_GET['lotNumber'])) {
        $lot_no = mysqli_real_escape_string($conn, $_GET['lotNumber']);
        $sql = "SELECT * FROM products WHERE lot_no = '$lot_no'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $found = true;
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>" . htmlspecialchars($row["id"]) . "</td>
                        <td>" . htmlspecialchars($row["serial_code"]) . "</td>
                        <td>" . htmlspecialchars($row["lot_no"]) . "</td>
                        <td>" . htmlspecialchars($row["name"]) . "</td>
                        <td>" . htmlspecialchars($row["price"]) . "</td>
                        <td>" . htmlspecialchars($row["stock"]) . "</td>
                        <td>" . htmlspecialchars($row["maintaining_level"]) . "</td>
                        <td>
                            <a href='add_quantity.php?lot_no=" . urlencode($row["lot_no"]) . "' class='btn btn-primary'>Edit</a>
                        </td>
                    </tr>";
            }
        }
    }

    if (!$found) {
        echo "<tr><td colspan='8'>No products found.</td></tr>";
    }

    echo "</table>";
}
?>

<h2>INVENTORY TABLE</h2>

<!-- Inventory Table -->
<table class='table'>
    <tr>
        <th>ID</th>
        <th>Serial Code</th>
        <th>Lot Number</th>
        <th>Name</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Maintaining Level</th>
        <th>Date Received</th>
        <th>Product Age (Days)</th>
        <th>Action</th>
    </tr>

    <?php
    $sql = "SELECT p.*, DATEDIFF(CURDATE(), p.date_received) AS product_age, po_i.product_id
    FROM products p
    LEFT JOIN purchase_order_items po_i ON p.id = po_i.product_id
    WHERE p.product_status = 'Verified'
    GROUP BY p.id;
    ";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                <td>" . htmlspecialchars($row["id"]) . "</td>
                <td>" . htmlspecialchars($row["serial_code"]) . "</td>
                <td>" . htmlspecialchars($row["lot_no"]) . "</td>
                <td>" . htmlspecialchars($row["name"]) . "</td>
                <td>₱" . htmlspecialchars($row["price"]) . "</td>
                <td>" . htmlspecialchars($row["stock"]) . "</td>
                <td>" . htmlspecialchars($row["maintaining_level"]) . "</td>
                <td>" . htmlspecialchars($row["date_received"]) . "</td>
                <td>" . htmlspecialchars($row["product_age"]) . " days</td>
                <td> <a href='product_transaction.php?id=" . $row["product_id"] . "' class='btn btn-primary mt-2'>View</a> </td> 
                <!-- dapat dito na fefetch yung id sa purchase_order_items -->

            </tr>";
        }
    } else {
        echo "<tr><td colspan='9'>No products found.</td></tr>";
    }
    ?>
</table>

<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>


<h3>Temporary Inventory</h3>


<?php

$sql = "SELECT *
        FROM inventory i
        GROUP BY i.product_code";

$sql_query = mysqli_query($conn, $sql);


?>

<div>
    <h3>Purchase Order Product Lists</h3>
    <table class="table">
        <tr>
            <th>Date Received</th>
            <th>Product Code</th>
            <th>Lot Number</th>
            <th>Category</th>
            <th>Number of Cans</th>
            <th>Pack Size</th>
            <th>Liters</th>
            <th>Reorder Level</th>
            <th>Maintaining Level</th>
            <th>Expiration Date</th>
            <th>Manufacturer</th>
            <th>Vendor</th>
            <th>Description</th>
            <th>Notes</th>
            <th>SG</th>
            <th>Action</th>
        </tr>
        <?php if (mysqli_num_rows($sql_query) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($sql_query)): ?>
                <tr>
                    <td><?= $row['date_received'] ?></td>
                    <td><?= $row['product_code'] ?></td>
                    <td><?= $row['lot_no'] ?></td>
                    <td><?= $row['category'] ?></td>
                    <td><?= $row['no_of_cans'] ?></td>
                    <td><?= $row['pack_size'] ?></td>
                    <td><?= $row['liters'] ?></td>
                    <td><?= $row['reorder_level'] ?></td>
                    <td><?= $row['maintaining_level'] ?></td>
                    <td><?= $row['expiration_date'] ?></td>
                    <td><?= $row['manufacturer'] ?></td>
                    <td><?= $row['vendor'] ?></td>
                    <td><?= $row['description'] ?></td>
                    <td><?= $row['notes'] ?></td>
                    <td><?= $row['sg'] ?></td>
                    <td>
                        <button type="button"
                            class="btn btn-success"
                            data-bs-toggle="modal"
                            data-bs-target="#viewProductModal"
                            data-id="<?= htmlspecialchars($row['product_code']) ?>"
                            onclick="openProductModal('<?= htmlspecialchars($row['product_code']) ?>')">
                            Click me
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="17">No list of product available.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

<div class="modal fade" id="viewProductModal" tabindex="-1" aria-labelledby="viewProductLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewProductLabel">View Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <tbody id="modalProductDetails">
                        <!-- Product details will be inserted here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function openProductModal(productId) {
        // Clear previous details
        const modalBody = document.getElementById("modalProductDetails");
        modalBody.innerHTML = ""; // Clear existing content

        // Find the row that contains the product ID
        const rows = document.querySelectorAll("table tr");
        rows.forEach(row => {
            const codeCell = row.querySelector("td:nth-child(2)"); // Assuming product_code is in the 2nd column
            if (codeCell && codeCell.textContent === productId) {
                // Create rows for the modal
                const details = [{
                        label: "Product Code",
                        value: codeCell.textContent
                    },
                    {
                        label: "Lot Number",
                        value: row.cells[2].textContent
                    },
                    {
                        label: "Category",
                        value: row.cells[3].textContent
                    },
                    {
                        label: "Number of Cans",
                        value: row.cells[4].textContent
                    },
                    {
                        label: "Pack Size",
                        value: row.cells[5].textContent
                    },
                    {
                        label: "Liters",
                        value: row.cells[6].textContent
                    },
                    {
                        label: "Reorder Level",
                        value: row.cells[7].textContent
                    },
                    {
                        label: "Maintaining Level",
                        value: row.cells[8].textContent
                    },
                    {
                        label: "Expiration Date",
                        value: row.cells[9].textContent
                    },
                    {
                        label: "Manufacturer",
                        value: row.cells[10].textContent
                    },
                    {
                        label: "Vendor",
                        value: row.cells[11].textContent
                    },
                    {
                        label: "Description",
                        value: row.cells[12].textContent
                    },
                    {
                        label: "Notes",
                        value: row.cells[13].textContent
                    },
                    {
                        label: "SG",
                        value: row.cells[14].textContent
                    }
                ];

                details.forEach(detail => {
                    const newRow = document.createElement("tr");
                    newRow.innerHTML = `<td><strong>${detail.label}:</strong> ${detail.value}</td>`;
                    modalBody.appendChild(newRow);
                });
            }
        });
    }
</script>

<?php include "includes/footer.php"; ?>



<!-- LATEST -->
<!-- dapat kada row ng product, meron button sa gilid, tapos pag pindot, napupunta sa ibang page tapos nakikita lahat ng transaction na binili yung
 product na yun  -->

<!-- Modal siya tapos nafifilter by date, tapos may pagination -->




<!-- Must be able to add new product -->
<!-- Must be able to add quantity to the same product na existing -->
<!-- Must notify user when the product is low or depleting -->
<!-- Implement Batch No. and Serial Code with barcode scanning (gamitin yung scratch it cards as an example) -->


<!-- nakalimutan mag pull woops new learnings -->


<!-- hmmm new problem, pano kung nag order ng same product tapos iaadd sa inventory, hindi naman pwede pag samahin yung two products na mag ka iba na ng takbo yung age ng product -->
<!-- Example: -->

<!-- Existing: Black Paint = (serial_code) 1234567890, (lot_number) 1020304050, (age) 50 days ago, (remaining_stock) 100 -->
<!-- New Arrival: Black Paint = (serial_code) 1234567890, (lot_number) 1020304050, (age) 0 days ago, (arrived) 500 -->

<!-- both are the same, pero bawal pag samahin yung dalawa. Siguro dapat pwede sila mag merge dinamically yung existing quantity, and na sesegregate sila by age. -->
<!-- Mag kakaroon ng revision sa Inventory -->





<!-- MARCH 21, 2025 -->
<!-- dapat kada product meron view all transaction tapos na didisplay lahat ng COS na nacreate na kasama dun yung product na inorder -->




<!-- April 8, 2025 -->
<!-- if may same product na napupunta sa inventory, dapat dynamic silang lahat, exisitng (pink), new arrivied (pink) yung quantity nila is dapat mag merge, pero nakikita sila separately -->
<!-- idea is dapat meron parang merge product then meron drop down? concept idk pa antok na ko HAHHAHA -->

<!-- DELETE NIYO NALANG YUNG MGA TRINATRANSFER NIYO NA PRODUCT SA INVENTORY -->
<!-- DELETE NIYO NALANG YUNG MGA TRINATRANSFER NIYO NA PRODUCT SA INVENTORY -->
<!-- DELETE NIYO NALANG YUNG MGA TRINATRANSFER NIYO NA PRODUCT SA INVENTORY -->
<!-- DELETE NIYO NALANG YUNG MGA TRINATRANSFER NIYO NA PRODUCT SA INVENTORY -->
<!-- DELETE NIYO NALANG YUNG MGA TRINATRANSFER NIYO NA PRODUCT SA INVENTORY -->