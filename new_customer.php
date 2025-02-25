<title>
    Add New Customer
</title>

<?php
include "includes/conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $address = $_POST["address"];

    mysqli_query($conn, "INSERT INTO customers (name, email, address) VALUES ('$name', '$email', '$address')");
    header("Location: index.php");
}
?>

<h2>Add New Customer</h2>
<form method="post">
    Name: <input type="text" name="name" required><br>
    Email: <input type="text" name="email" required><br>
    Address: <input type="text" name="address" required><br>
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="index.php" class="btn btn-secondary">Back</a>
</form>

<?php

include "includes/footer.php";

?>