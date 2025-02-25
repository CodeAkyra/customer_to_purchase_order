<title>Dashboard</title>

<?php
require "includes/conn.php";

// Query to count products below maintaining level
$queryLowStock = "SELECT COUNT(*) AS low_stock_count FROM products WHERE stock < maintaining_level";
$resultLowStock = mysqli_query($conn, $queryLowStock);
$rowLowStock = mysqli_fetch_assoc($resultLowStock);
$lowStockCount = $rowLowStock["low_stock_count"];

// Query to count total customers
$queryCustomers = "SELECT COUNT(*) AS total_customers FROM customers";
$resultCustomers = mysqli_query($conn, $queryCustomers);
$rowCustomers = mysqli_fetch_assoc($resultCustomers);
$totalCustomers = $rowCustomers["total_customers"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file -->
</head>

<body>
    <h2>Dashboard</h2>

    <div class="dashboard-container">
        <div class="dashboard-card">
            <h3>Number of Products Below Maintaining Level:</h3>
            <p class="low-stock"><?= $lowStockCount ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Total Number of Customers:</h3>
            <p class="total-customers"><?= $totalCustomers ?></p>
        </div>
    </div>

    <a href="inventory.php" class="btn btn-primary">Go to Inventory</a>
    <a href="index.php" class="btn btn-primary">Customer Information</a>
</body>

</html>

<style>
    body {
        font-family: Arial, sans-serif;
        text-align: center;
        padding: 20px;
    }

    .dashboard-container {
        display: flex;
        justify-content: center;
        gap: 20px;
    }

    .dashboard-card {
        background: #f8d7da;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        font-size: 18px;
        min-width: 250px;
    }

    .low-stock {
        font-size: 40px;
        color: red;
        font-weight: bold;
    }

    .total-customers {
        font-size: 40px;
        color: #007bff;
        font-weight: bold;
    }

    .btn {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 15px;
        text-decoration: none;
        background: #007bff;
        color: white;
        border-radius: 5px;
    }

    .btn:hover {
        background: #0056b3;
    }
</style>