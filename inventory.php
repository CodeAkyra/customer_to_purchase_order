<?php
include "conn.php";
?>

<h2>Inventory</h2>
<?php

$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql); {
    echo "<table class='table'>";
    echo "<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Price</th>
    <th>Stock</th>
    </tr>";

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
            <td>{$row["id"]}</td>
            <td>{$row["name"]}</td>
            <td>{$row["price"]}</td>
            <td>{$row["stock"]}</td>
            </tr>";
        };
    } else {
        echo "No Product Found!";
    }
    echo "</table>";
}

?>


<a href="index.php"> Customer Information </a>