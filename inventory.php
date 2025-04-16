<title>Inventory</title>
<?php require "includes/conn.php"; ?>

<h2 class="text-center mb-4">Inventory</h2>

<!-- Search Product ( BROKEN )-->
<form action="" method="get">
    <h3>SEARCH PRODUCT BY SERIAL CODE OR LOT NUMBER</h3>
    <label>Enter Product Code</label>
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
            <th>Product Code</th>
            <th>Lot Number</th>
            <th>Name</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Maintaining Level</th>
            <th>Action</th>
        </tr>";

    $found = false;

    if (!empty($_GET['serialCode'])) {
        $product_code = mysqli_real_escape_string($conn, $_GET['serialCode']);
        $sql = "SELECT * FROM products WHERE product_code = '$product_code'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $found = true;
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>" . htmlspecialchars($row["id"]) . "</td>
                        <td>" . htmlspecialchars($row["product_code"]) . "</td>
                        <td>" . htmlspecialchars($row["lot_no"]) . "</td>
                        <td>" . htmlspecialchars($row["description"]) . "</td>
                        <td>" . htmlspecialchars($row["price"]) . "</td>
                        <td>" . htmlspecialchars($row["stock"]) . "</td>
                        <td>" . htmlspecialchars($row["maintaining_level"]) . "</td>
                        <td>
                            <a href='add_quantity.php?product_code=" . urlencode($row["product_code"]) . "' class='btn btn-primary'>Edit</a>
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
                        <td>" . htmlspecialchars($row["product_code"]) . "</td>
                        <td>" . htmlspecialchars($row["lot_no"]) . "</td>
                        <td>" . htmlspecialchars($row["description"]) . "</td>
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

<div class="container my-5">
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>ID</th>
                    <th>Date Received</th>
                    <th>Product Code</th>
                    <th>Lot No.</th>
                    <th>Category</th>
                    <th>No. of Cans</th>
                    <th>Pack Size</th>
                    <th>Liters</th>
                    <th>Re-order Level</th>
                    <th>Maintaining Level</th>
                    <th>Expiration Date</th>
                    <th>Manufacturer</th>
                    <th>Vendor</th>
                    <th>Description</th>
                    <th style="min-width: 200px;">Notes</th>
                    <th>SG</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Product Age</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT p.*, DATEDIFF(CURDATE(), p.date_received) AS product_age, po_i.product_id
                        FROM products p
                        LEFT JOIN purchase_order_items po_i ON p.id = po_i.product_id
                        WHERE p.product_status = 'Verified'
                        GROUP BY p.id";
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <td class='text-center'>" . htmlspecialchars($row["id"]) . "</td>
                            <td>" . htmlspecialchars($row["date_received"]) . "</td>
                            <td>" . htmlspecialchars($row["product_code"]) . "</td>
                            <td>" . htmlspecialchars($row["lot_no"]) . "</td>
                            <td>" . htmlspecialchars($row["category"]) . "</td>
                            <td class='text-center'>" . htmlspecialchars($row["no_of_cans"]) . "</td>
                            <td>" . htmlspecialchars($row["pack_size"]) . "</td>
                            <td>" . htmlspecialchars($row["liters"]) . "</td>
                            <td class='text-center'>" . htmlspecialchars($row["reorder_level"]) . "</td>
                            <td class='text-center'>" . htmlspecialchars($row["maintaining_level"]) . "</td>
                            <td>" . htmlspecialchars($row["expiration_date"]) . "</td>
                            <td>" . htmlspecialchars($row["manufacturer"]) . "</td>
                            <td>" . htmlspecialchars($row["vendor"]) . "</td>
                            <td>" . htmlspecialchars($row["description"]) . "</td>
                            <td style='min-width: 200px;'>" . htmlspecialchars($row["notes"]) . "</td>
                            <td>" . htmlspecialchars($row["sg"]) . "</td>
                            <td>₱" . number_format($row["price"], 2) . "</td>
                            <td class='text-center'>" . htmlspecialchars($row["stock"]) . "</td>
                            <td class='text-center'>" . htmlspecialchars($row["product_age"]) . " days</td>
                            <td class='text-center'>
                                <a href='product_transaction.php?id=" . htmlspecialchars($row["product_id"]) . "' class='btn btn-sm btn-primary'>
                                    View
                                </a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='20' class='text-center text-muted'>No verified products found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>


<?php include "includes/footer.php"; ?>



<!-- LATEST -->
<!-- dapat kada row ng product, meron button sa gilid, tapos pag pindot, napupunta sa ibang page tapos nakikita lahat ng transaction na binili yung
 product na yun  -->

<!-- Modal siya tapos nafifilter by date, tapos may pagination -->




<!-- Must be able to add new product -->
<!-- Must be able to add quantity to the same product na existing -->
<!-- Must notify user when the product is low or depleting -->
<!-- Implement Batch No. and Product Code with barcode scanning (gamitin yung scratch it cards as an example) -->


<!-- nakalimutan mag pull woops new learnings -->


<!-- hmmm new problem, pano kung nag order ng same product tapos iaadd sa inventory, hindi naman pwede pag samahin yung two products na mag ka iba na ng takbo yung age ng product -->
<!-- Example: -->

<!-- Existing: Black Paint = (product_code) 1234567890, (lot_number) 1020304050, (age) 50 days ago, (remaining_stock) 100 -->
<!-- New Arrival: Black Paint = (product_code) 1234567890, (lot_number) 1020304050, (age) 0 days ago, (arrived) 500 -->

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