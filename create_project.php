<title>Create New Project</title>

<?php
require "includes/conn.php";
$customer_id = $_GET['customer_id'] ?? null;

// Fetch customer details
$customerQuery = "SELECT * FROM customers WHERE id = $customer_id";
$customerResult = mysqli_query($conn, $customerQuery);
$customer = mysqli_fetch_assoc($customerResult);

// Handle Project Creation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_name = mysqli_real_escape_string($conn, $_POST['project_name']);
    $date_started = date("Y-m-d"); // Defaults to today’s date

    $insertQuery = "INSERT INTO project (customer_id, project_name, date_started) VALUES ($customer_id, '$project_name', '$date_started')";
    if (mysqli_query($conn, $insertQuery)) {
        $new_project_id = mysqli_insert_id($conn);
        header("Location: create_po.php?customer_id=$customer_id&selected_project=$new_project_id");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<h2>Create New Project</h2>
<p><strong>Customer:</strong> <?= $customer['name'] ?></p>
<p><strong>Address:</strong> <?= $customer['address'] ?></p>

<form method="post">
    <div class="form-group">
        <label>Project Name:</label>
        <input type="text" name="project_name" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Create Project</button>
    <a href="create_po.php?customer_id=<?= $customer_id ?>" class="btn btn-secondary">Cancel</a>
</form>

<?php include "includes/footer.php"; ?>