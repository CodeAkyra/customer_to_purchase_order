<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

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