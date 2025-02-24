<?php
include "conn.php";
$id = $_GET["id"];
$customer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM customers WHERE id = $id"));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];

    mysqli_query($conn, "UPDATE customers SET name='$name', email='$email' WHERE id=$id");
    header("Location: index.php");
}
?>

<h2>Edit Customer</h2>
<form method="post">
    Name: <input type="text" name="name" value="<?= $customer['name'] ?>" required><br>
    Email: <input type="email" name="email" value="<?= $customer['email'] ?>" required><br>
    <button type="submit">Update</button>
</form>

<a href="index.php" class="btn btn-secondary">Back</a>