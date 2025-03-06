<title>Add New Customer</title>

<?php
require "includes/conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_btn'])) {
    // Sanitize input
    $name = $_POST["name"];
    $email = $_POST["email"];
    $address = $_POST["address"];

    // Insert customer using prepared statement
    $stmt = $conn->prepare("INSERT INTO customers (name, email, address) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $address);

    if ($stmt->execute()) {
        $customer_id = $stmt->insert_id; // Get the last inserted customer ID
        $stmt->close();

        // File Upload Logic (Only if customer is successfully inserted)
        if (isset($_FILES["choosefile"]) && $_FILES["choosefile"]["error"] == UPLOAD_ERR_OK) {
            $filename = $_FILES["choosefile"]["name"];
            $tempfile = $_FILES["choosefile"]["tmp_name"];
            $folder = "includes/uploads/" . $filename;
            $fileType = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $fileSize = $_FILES["choosefile"]["size"]; // Get file size

            if ($fileType != "pdf") {
                echo "<div class='alert alert-danger text-center'>Only PDF files are allowed!</div>";
            } else {
                $sql = "INSERT INTO uploads (customer_id, filename, file_type, file_size) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isss", $customer_id, $filename, $fileType, $fileSize);

                if ($stmt->execute()) {
                    if (move_uploaded_file($tempfile, $folder)) {
                        header("Location: customer_information.php"); // Redirect after success
                        exit();
                    } else {
                        echo "<div class='alert alert-danger text-center'>Failed to move uploaded file.</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger text-center'>Error uploading file: " . $conn->error . "</div>";
                }
                $stmt->close();
            }
        }

        // Redirect even if no file is uploaded
        header("Location: customer_information.php");
        exit();
    } else {
        echo "<div class='alert alert-danger text-center'>Error adding customer: " . $conn->error . "</div>";
    }
}
?>

<h2>Add New Customer</h2>
<form method="post" enctype="multipart/form-data">
    Name: <input type="text" name="name" required><br>
    Email: <input type="text" name="email" required><br>
    Address: <input type="text" name="address" required><br>
    <input type="file" class="form-control" name="choosefile">
    <button type="submit" class="btn btn-primary" name="submit_btn">Save</button>
    <a href="customer_information.php" class="btn btn-secondary">Back</a>
</form>

<?php include "includes/footer.php"; ?>