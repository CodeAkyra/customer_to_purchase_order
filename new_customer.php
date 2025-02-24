<?php
include "conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];

    mysqli_query($conn, "INSERT INTO customers (name, email) VALUES ('$name', '$email')");
    header("Location: index.php");
}
?>

<h2>Add New Customer</h2>
<form method="post">
    Name: <input type="text" name="name" required><br>
    Email: <input type="email" name="email" required><br>
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="index.php" class="btn btn-secondary">Back</a>
</form>