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

    mysqli_query($conn, "INSERT INTO purchase_orders (customer_id, project_id, agent_id, segment, sub_segment, date_of_cos, status) 
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

<h2 class="text-center mb-4">Create Customer Order Slip</h2>

<div class="container">
    <form method="post" id="orderForm">

        <div class="mb-4">
            <h4 class="text-muted">Customer Information</h4>
            <p><strong>Name:</strong> <?= $customer['name'] ?></p>
            <p><strong>Address:</strong> <?= $customer['address'] ?></p>
            <p><strong>TIN:</strong> <?= $customer['tin'] ?></p>
        </div>

        <div class="mb-4">
            <label for="agent_id" class="form-label">Select Agent</label>
            <select name="agent_id" class="form-select" required>
                <option value="" disabled selected>Select an Agent</option>
                <?php while ($agent = mysqli_fetch_assoc($agentResult)): ?>
                    <option value="<?= $agent['id'] ?>"><?= $agent['agent_name'] ?> - <?= $agent['agent_code'] ?> (<?= $agent['area'] ?>)</option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-4">
            <label for="segment" class="form-label">Select Segment</label>
            <select name="segment" class="form-select" required>
                <option value="" disabled selected>Select Segment</option>
                <option value="PROTECTIVE">PROTECTIVE</option>
                <option value="MARINE">MARINE</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="sub_segment" class="form-label">Select Sub-Segment</label>
            <select name="sub_segment" class="form-select" required>
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
        </div>

        <div class="mb-4">
            <label for="project_id" class="form-label">Select Project</label>
            <div class="d-flex align-items-center gap-3">
                <select name="project_id" id="projectSelect" class="form-select" required>
                    <option value="" disabled selected>Select a Project</option>
                    <?php while ($project = mysqli_fetch_assoc($projectResult)): ?>
                        <option value="<?= $project['project_id'] ?>"><?= $project['project_name'] ?></option>
                    <?php endwhile; ?>
                </select>
                <a href="create_project.php?customer_id=<?= $customer_id ?>" class="btn btn-outline-primary btn-sm">Create New Project</a>
            </div>
        </div>

        <div class="mb-4">
            <label for="productSelect" class="form-label">Select Products</label>
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#productModal">
                Choose from Inventory
            </button>
        </div>

        <!-- Product Selection Modal -->
        <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="productModalLabel">Select Product from Inventory</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" id="searchBar" class="form-control mb-4" placeholder="Search for a product...">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>Stock</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($product = mysqli_fetch_assoc($productResult)): ?>
                                    <tr>
                                        <td><?= $product['id'] ?></td>
                                        <td><?= $product['description'] ?></td>
                                        <td><?= $product['stock'] ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-success add-to-table"
                                                data-id="<?= $product['id'] ?>"
                                                data-name="<?= $product['description'] ?>"
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
        <div class="mb-4">
            <label for="selectedProductsTable" class="form-label">Selected Products</label>
            <table class="table table-bordered" id="selectedProductsTable">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Available Stock</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Create COS</button>
            <a href="customer_information.php" class="btn btn-secondary">Back</a>
        </div>

    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const selectedProductsTable = document.querySelector("#selectedProductsTable tbody");

        document.querySelectorAll(".add-to-table").forEach(button => {
            button.addEventListener("click", function(event) {
                event.preventDefault();

                let productId = this.dataset.id;
                let productName = this.dataset.name;
                let stock = this.dataset.stock;

                if (document.querySelector(`#row-${productId}`)) {
                    alert("Product is already added!");
                    return;
                }

                let newRow = document.createElement("tr");
                newRow.id = `row-${productId}`;
                newRow.innerHTML = `
                    <td>${productName}</td>
                    <td>${stock}</td>
                    <td>
                        <input type="number" name="products[${productId}]" min="1" max="${stock}" class="form-control" required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-product" data-id="${productId}">Remove</button>
                    </td>
                `;
                selectedProductsTable.appendChild(newRow);
            });
        });

        document.addEventListener("click", function(event) {
            if (event.target.classList.contains("remove-product")) {
                let productId = event.target.dataset.id;
                document.querySelector(`#row-${productId}`).remove();
            }
        });

        document.getElementById("searchBar").addEventListener("input", function() {
            let filter = this.value.toLowerCase();
            document.querySelectorAll("#productModal tbody tr").forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
            });
        });
    });
</script>


<?php include "includes/footer.php"; ?>