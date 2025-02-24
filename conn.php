<?php

$servername = "localhost";
$localname = "root";
$password = "";
$dbname = "customer_to_purchase_order";

$conn = mysqli_connect($servername, $localname, $password, $dbname);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error()); {
    }
} else {
    echo "Success!";
}
