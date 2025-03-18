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
    $servername = "localhost";
    $localname = "root";
    $password = "";
    $dbname = "capsdb";

    $conn = mysqli_connect($servername, $localname, $password, $dbname);

    if (!$conn) {
        die("Connection Failed: " . mysqli_connect_error()); {
        }
    } else {
        echo "Success!";
    }

    ?>