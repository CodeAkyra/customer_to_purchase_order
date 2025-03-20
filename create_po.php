<!-- CHANGING TO COS (CUSTOMER ORDER SLIP) -->
<title>Create Customer Order Slip</title>

<?php
require "includes/conn.php";
$customer_id = $_GET['customer_id'];

// Fetch customer details
$customerQuery = "SELECT * FROM customers WHERE id = $customer_id";
$customerResult = mysqli_query($conn, $customerQuery);
$customer = mysqli_fetch_assoc($customerResult);

// Fetch available products
$productQuery = "SELECT * FROM products";
$productResult = mysqli_query($conn, $productQuery);

// Fetch active projects for this customer
$projectQuery = "SELECT * FROM project WHERE customer_id = $customer_id AND date_ended IS NULL";
$projectResult = mysqli_query($conn, $projectQuery);

// Fetch agents
$agentQuery = "SELECT * FROM agents";
$agentResult = mysqli_query($conn, $agentQuery);

// Handle PO Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = date("Y-m-d H:i:s");
    $project_id = $_POST['project_id'];
    $agent_id = $_POST['agent_id'];
    $segment = $_POST['segment'];
    $sub_segment = $_POST['sub_segment'];

    mysqli_query($conn, "INSERT INTO purchase_orders (customer_id, project_id, agent_id, segment, sub_segment, order_date, status) 
                         VALUES ($customer_id, $project_id, $agent_id, '$segment', '$sub_segment', '$date', 'Pending')");
    $po_id = mysqli_insert_id($conn);

    foreach ($_POST['products'] as $product_id => $quantity) {
        if ($quantity > 0) {
            $priceQuery = mysqli_query($conn, "SELECT price FROM products WHERE id = $product_id");

            if ($priceQuery && mysqli_num_rows($priceQuery) > 0) {
                $priceRow = mysqli_fetch_assoc($priceQuery);
                $price = $priceRow['price'];
            } else {
                $price = 0;
            }

            if ($price > 0) {
                $subtotal = $price * $quantity;
                $insertQuery = "INSERT INTO purchase_order_items (po_id, product_id, quantity, price, subtotal) 
                                VALUES ('$po_id', '$product_id', '$quantity', '$price', '$subtotal')";

                if (!mysqli_query($conn, $insertQuery)) {
                    die("Error inserting order item: " . mysqli_error($conn));
                }
            }
        }
    }
    header("Location: view_po.php?id=$po_id");
    exit();
}
?>

<h2>Create Customer Order Slip</h2>
<p><strong>Customer:</strong> <?= $customer['name'] ?></p>
<p><strong>Address:</strong> <?= $customer['address'] ?></p>

<!-- Start Form -->
<form method="post" id="orderForm">

    <h3>Select Agent</h3>
    <select name="agent_id" class="form-control" required>
        <option value="" disabled selected>Select an Agent</option>
        <?php while ($agent = mysqli_fetch_assoc($agentResult)): ?>
            <option value="<?= $agent['id'] ?>"><?= $agent['agent_name'] ?> - <?= $agent['agent_code'] ?> (<?= $agent['area'] ?>)</option>
        <?php endwhile; ?>
    </select>

    <h3>Select Segment</h3>
    <select name="segment" class="form-control" required>
        <option value="" disabled selected>Select Segment</option>
        <option value="PROTECTIVE">PROTECTIVE</option>
        <option value="MARINE">MARINE</option>
    </select>

    <h3>Select Sub-Segment</h3>
    <select name="sub_segment" class="form-control" required>
        <option value="" disabled selected>Select Sub-Segment</option>
        <option value="FLOOR COATING">Floor Coating</option>
        <option value="INFRASTRUCTURE">Infrastructure</option>
        <option value="MINING">Mining</option>
        <option value="OIL & GAS">Oil & Gas</option>
        <option value="OTHERS">Others</option>
        <option value="POWER PLANT">Power Plant</option>
        <option value="LABOR">Labor</option>
        <option value="CREDIT MEMO">Credit Memo</option>
        <option value="DELIVERY CHARGE">Delivery Charge</option>
        <option value="TECHNICAL CHARGE">Technical Charge</option>
    </select>

    <h3>Select Project</h3>
    <div style="display: flex; align-items: center; gap: 10px;">
        <select name="project_id" id="projectSelect" class="form-control" required>
            <option value="" disabled selected>Select a project</option>
            <?php while ($project = mysqli_fetch_assoc($projectResult)): ?>
                <option value="<?= $project['project_id'] ?>"><?= $project['project_name'] ?></option>
            <?php endwhile; ?>
        </select>
        <a href="create_project.php?customer_id=<?= $customer_id ?>" class="btn btn-primary">Create New Project</a>
    </div>

    <h3>Select Products</h3>

    <!-- Button to Open Modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
        Select from Inventory
    </button>

    <!-- Product Selection Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Select Product from Inventory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Search Bar -->
                    <input type="text" id="searchBar" class="form-control" placeholder="Search product...">

                    <table class="table" id="productTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Serial Code</th>
                                <th>Lot Number</th>
                                <th>Product</th>
                                <th>Stock</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($product = mysqli_fetch_assoc($productResult)): ?>
                                <tr>
                                    <td><?= $product['id'] ?></td>
                                    <td><?= $product['serial_code'] ?></td>
                                    <td><?= $product['lot_no'] ?></td>
                                    <td><?= $product['name'] ?></td>
                                    <td><?= $product['stock'] ?></td>
                                    <td>
                                        <button class="btn btn-success add-to-table"
                                            data-id="<?= $product['id'] ?>"
                                            data-name="<?= $product['name'] ?>"
                                            data-serial="<?= $product['serial_code'] ?>"
                                            data-lot="<?= $product['lot_no'] ?>"
                                            data-stock="<?= $product['stock'] ?>">
                                            Add
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Selected Products Table -->
    <table class="table" id="selectedProductsTable">
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Serial Code</th>
                <th>Lot Number</th>
                <th>Product</th>
                <th>Available Stock</th>
                <th>Quantity</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <button type="submit" class="btn btn-success">Create COS</button>
    <a href="customer_information.php" class="btn btn-secondary">Back</a>
</form>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const selectedProductsTable = document.querySelector("#selectedProductsTable tbody");
        const projectSelect = document.querySelector("#projectSelect");

        document.querySelectorAll(".add-to-table").forEach(button => {
            button.addEventListener("click", function(event) {
                event.preventDefault(); // Prevents form submission

                let productId = this.dataset.id;
                let productName = this.dataset.name;
                let serialCode = this.dataset.serial;
                let lotNumber = this.dataset.lot;
                let stock = this.dataset.stock;

                let tableBody = document.querySelector("#selectedProductsTable tbody");

                // Check if the product is already added
                if (document.querySelector(`#row-${productId}`)) {
                    alert("Product is already added!");
                    return;
                }

                // Create table row
                let newRow = document.createElement("tr");
                newRow.id = `row-${productId}`;
                newRow.innerHTML = `
            <td>${productId}</td>
            <td>${serialCode}</td>
            <td>${lotNumber}</td>
            <td>${productName}</td>
            <td>${stock}</td>
            <td>
                <input type="number" name="products[${productId}]" min="1" max="${stock}" class="form-control" required>
            </td>
            <td>
                <button type="button" class="btn btn-danger remove-product" data-id="${productId}">Remove</button>
            </td>
        `;

                // Append to table
                tableBody.appendChild(newRow);
            });
        });

        // Remove Product
        document.addEventListener("click", function(event) {
            if (event.target.classList.contains("remove-product")) {
                let productId = event.target.dataset.id;
                document.querySelector(`#row-${productId}`).remove();
            }
        });


        // Search Function
        document.getElementById("searchBar").addEventListener("input", function() {
            let filter = this.value.toLowerCase();
            document.querySelectorAll("#productTable tbody tr").forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
            });
        });
    });
</script>

<?php include "includes/footer.php"; ?>