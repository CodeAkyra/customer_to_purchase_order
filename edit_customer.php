<?php
require "includes/conn.php";

$id = intval($_GET["id"]); // Converts to an integer to prevent SQL injection

// Fetch customer details
$customer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM customers WHERE id = $id"));

// Fetch uploaded file for this customer
$upload = mysqli_fetch_assoc(mysqli_query($conn, "SELECT filename FROM uploads WHERE customer_id = $id"));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $address = $_POST["address"];

    // Update customer details
    mysqli_query($conn, "UPDATE customers SET name='$name', email='$email', address='$address' WHERE id=$id");

    // Handle file upload
    if (isset($_FILES["choosefile"]) && $_FILES["choosefile"]["error"] == UPLOAD_ERR_OK) {
        $filename = $_FILES["choosefile"]["name"];
        $tempfile = $_FILES["choosefile"]["tmp_name"];
        $folder = "includes/uploads/" . $filename;
        $fileType = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $fileSize = $_FILES["choosefile"]["size"];

        if ($fileType != "pdf") {
            echo "<div class='alert alert-danger text-center'>Only PDF files are allowed!</div>";
        } else {
            // Check if a file already exists for this customer
            $existing_file = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM uploads WHERE customer_id = $id"));

            if ($existing_file) {
                // Update existing file
                $upload_id = $existing_file['id'];
                $sql = "UPDATE uploads SET filename=?, file_type=?, file_size=?, uploaded_at=NOW() WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssii", $filename, $fileType, $fileSize, $upload_id);
            } else {
                // Insert new file
                $sql = "INSERT INTO uploads (customer_id, filename, file_type, file_size, uploaded_at) VALUES (?, ?, ?, ?, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isss", $id, $filename, $fileType, $fileSize);
            }

            if ($stmt->execute()) {
                move_uploaded_file($tempfile, $folder);
                header("Location: customer_information.php");
                exit();
            } else {
                echo "<div class='alert alert-danger text-center'>Error uploading file: " . $conn->error . "</div>";
            }
            $stmt->close();
        }
    }
}
?>

<div class="container mt-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4>Edit Customer</h4>
        </div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Name:</label>
                    <input type="text" class="form-control" name="name" value="<?= $customer['name'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email:</label>
                    <input type="email" class="form-control" name="email" value="<?= $customer['email'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address:</label>
                    <input type="text" class="form-control" name="address" value="<?= $customer['address'] ?>" required>
                </div>

                <!-- Uploaded File Section -->
                <?php if (!empty($upload['filename'])): ?>
                    <div class="mb-3">
                        <div class="card">
                            <div class="card-header bg-secondary text-white">
                                <strong>Uploaded File</strong>
                            </div>
                            <div class="card-body">
                                <p class="mb-2">
                                    <a href="includes/uploads/<?= $upload['filename'] ?>" target="_blank" class="btn btn-sm btn-info">
                                        <strong style="color: white;">VIEW FILE IN NEW TAB</strong>
                                    </a>
                                </p>
                                <div class="ratio ratio-16x9">
                                    <iframe src="includes/uploads/<?= $upload['filename'] ?>" class="border rounded"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label">Upload New File (PDF only):</label>
                    <input type="file" class="form-control" name="choosefile">
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="customer_information.php" class="btn btn-secondary">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>