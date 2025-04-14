<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>


<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="inventory.php">Inventory</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="customer_information.php">Customer Information</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="project.php">Project</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="customer_order_slip.php">Customer Order Slip</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="delivery_receipt.php">Delivery Receipt</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="sales_invoice.php">Sales Invoice</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="po_products.php">Purchase Order Products</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<body>

    <?php
    // $servername = "localhost";
    // $localname = "root";
    // $password = "";
    // // $dbname = "customer_to_purchase_order";
    // $dbname = "capsdb1";

    // $conn = mysqli_connect($servername, $localname, $password, $dbname);

    // if (!$conn) {
    //     die("Connection Failed: " . mysqli_connect_error()); {
    //     }
    // } else {
    //     echo "Success!";
    // }

    $servername = "srv1858.hstgr.io";
    $username = "u881006464_inkote_test";
    $password = "X5z0CzKjgcmQ51";
    $dbname = "u881006464_test_db";
    $port = "3306";

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = new mysqli($servername, $username, $password, $dbname, $port);
    $conn->set_charset('utf8mb4');

    if ($conn->connect_error) {
        die('Error:' . $conn->connect_error);
    }
    ?>