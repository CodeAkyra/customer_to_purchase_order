<title>Create Purchase Order Product</title>
<?php
require "includes/conn.php";
$message = "";

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['po_description']) && isset($_POST['csv_data'])) {
    $description = mysqli_real_escape_string($conn, $_POST['po_description']);
    $order_date = mysqli_real_escape_string($conn, $_POST['po_order_date']);
    $price = floatval($_POST['po_price']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Check if CSV data is set and not empty
    $csvData = isset($_POST['csv_data']) ? json_decode($_POST['csv_data'], true) : null;

    if ($csvData && is_array($csvData)) {
        // Insert PO
        $query = "INSERT INTO purchase_order_product (po_description, po_order_date, po_price, status)
                  VALUES ('$description', '$order_date', $price, '$status')";
        if (mysqli_query($conn, $query)) {
            $id = mysqli_insert_id($conn); // Get the new PO's ID

            // Insert CSV rows into product list with that PO ID
            foreach ($csvData as $row) {
                // Ensure the row has at least 15 columns
                if (count($row) < 15) {
                    continue; // Skip rows with missing columns
                }

                // Clean up each value for SQL insertion
                $columns = array_map(fn($item) => mysqli_real_escape_string($conn, $item), $row);

                // Insert into the product list
                $insert = "INSERT INTO purchase_order_product_list 
                (pop_id, date_received, product_code, lot_no, category, no_of_cans, pack_size, liters, reorder_level, maintaining_level, expiration_date, manufacturer, vendor, description, notes, sg)
                VALUES (
                    '$id', '{$columns[0]}', '{$columns[1]}', '{$columns[2]}', '{$columns[3]}', '{$columns[4]}', '{$columns[5]}', '{$columns[6]}',
                    '{$columns[7]}', '{$columns[8]}', '{$columns[9]}', '{$columns[10]}', '{$columns[11]}', '{$columns[12]}', '{$columns[13]}', '{$columns[14]}'
                )";

                // Execute the query to insert data
                if (!mysqli_query($conn, $insert)) {
                    $message = "<div class='alert alert-danger'>Insert error: " . mysqli_error($conn) . "</div>";
                    break; // Stop if there is any insert error
                }
            }

            if (empty($message)) {
                $message = "<div class='alert alert-success'>Purchase Order and Product List created successfully!</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>CSV data is empty or not in the correct format.</div>";
    }
}
?>

<!-- HTML Form -->
<a href="po_products.php" class="btn btn-primary">Back</a>

<div class="container mt-3">
    <h3>Create Purchase Order Product</h3>
    <?= $message ?>
    <form method="POST" id="poForm">
        <!-- PO Info -->
        <div class="mb-3">
            <label for="po_description" class="form-label">Description</label>
            <input type="text" name="po_description" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="po_order_date" class="form-label">Order Date</label>
            <input type="date" name="po_order_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="po_price" class="form-label">Price</label>
            <input type="number" name="po_price" class="form-control" step="0.01" required>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <option value="Pending">Pending</option>
                <option value="Completed">Completed</option>
            </select>
        </div>

        <!-- CSV Upload & Preview -->
        <hr>
        <h4>Import Product List via CSV</h4>
        <input type="file" id="csvFileInput" accept=".csv" class="form-control w-50" required>
        <button class="btn btn-warning my-2" type="button" onclick="previewCSV()">Preview CSV</button>
        <div id="csvPreviewArea"></div>
        <input type="hidden" name="csv_data" id="csvDataInput">
        <button type="submit" class="btn btn-success mt-3">Create Purchase Order with Product List</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/papaparse@5.4.1/papaparse.min.js"></script>
<script>
    function previewCSV() {
        const fileInput = document.getElementById("csvFileInput");
        const file = fileInput.files[0];
        const previewArea = document.getElementById("csvPreviewArea");
        const csvDataInput = document.getElementById("csvDataInput");

        if (!file) {
            alert("Please select a CSV file.");
            return;
        }

        Papa.parse(file, {
            skipEmptyLines: true,
            complete: function(results) {
                let allRows = results.data;

                // Remove completely empty rows or invalid rows
                allRows = allRows.filter(row => row.length >= 15 && row.join('').trim() !== '');

                if (allRows.length <= 1) {
                    previewArea.innerHTML = "<p class='text-danger'>No valid data found in the CSV.</p>";
                    return;
                }

                const headers = allRows[0]; // First row = header
                const data = allRows.slice(1); // Skip the header row

                // Preview table
                let html = "<table class='table table-bordered'><thead><tr>";
                headers.forEach(header => html += `<th>${header}</th>`);
                html += "</tr></thead><tbody>";

                data.forEach(row => {
                    // Convert date_received (row[0]) to YYYY-MM-DD
                    const receivedParts = row[0]?.split('/');
                    if (receivedParts?.length === 3) {
                        const [dd, mm, yyyy] = receivedParts;
                        row[0] = `${yyyy}-${mm.padStart(2, '0')}-${dd.padStart(2, '0')}`;
                    }

                    // Convert expiration_date (row[10]) to YYYY-MM-DD
                    const expirationParts = row[9]?.split('/');
                    if (expirationParts?.length === 3) {
                        const [dd, mm, yyyy] = expirationParts;
                        row[9] = `${yyyy}-${mm.padStart(2, '0')}-${dd.padStart(2, '0')}`;
                    }

                    html += "<tr>";
                    row.forEach(col => html += `<td>${col}</td>`);
                    html += "</tr>";
                });

                html += "</tbody></table>";
                previewArea.innerHTML = html;

                // Save data (no headers) to hidden input, now with formatted dates
                csvDataInput.value = JSON.stringify(data);
            },
            error: function(err) {
                alert("Error parsing CSV: " + err.message);
            }
        });
    }
</script>


<?php include "includes/footer.php"; ?>




<!-- dapat nakakapag upload tapos nakakapag create, pero meron way pala ma replace yung unang create dun sa inupload na same lang din pero complete ang details -->

<!-- pre-order (ilagay na yung mga products na kailangan, pero di pa complete details) -->

<!-- binigay yung list sa inkote with full details, upload sa system, tapos yung unang list mawawala na or siguro ma ooverwrite? -->