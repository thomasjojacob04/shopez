<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

// Database connection
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "ecommerce";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0.00;
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';

    // Validate input
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Product name is required";
    }
    
    if ($price <= 0) {
        $errors[] = "Price must be greater than zero";
    }
    
    if (empty($description)) {
        $errors[] = "Description is required";
    }

    if (empty($errors)) {
        try {
            // Start transaction
            mysqli_begin_transaction($conn);

            $new_image_name = "";

            // Handle image upload if a new image was provided
            if (!empty($_FILES['image']['name'])) {
                // Get current image name
                $query = "SELECT image FROM products WHERE id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "i", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $current_image = mysqli_fetch_assoc($result)['image'];

                // Process new image
                $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (!in_array($file_extension, $allowed_extensions)) {
                    throw new Exception("Invalid file type. Only JPG, PNG, and GIF files are allowed.");
                }

                // Generate unique filename
                $new_image_name = uniqid() . '.' . $file_extension;
                $upload_path = 'uploads/products/' . $new_image_name;

                // Upload new image
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    throw new Exception("Failed to upload image.");
                }

                // Delete old image if it exists
                if ($current_image && file_exists('uploads/products/' . $current_image)) {
                    unlink('uploads/products/' . $current_image);
                }

                // Update query with image
                $query = "UPDATE products SET name = ?, price = ?, description = ?, image = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "sdssi", $name, $price, $description, $new_image_name, $id);
            } else {
                // Update query without image
                $query = "UPDATE products SET name = ?, price = ?, description = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "sdsi", $name, $price, $description, $id);
            }

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error updating product in database.");
            }

            // Commit transaction
            mysqli_commit($conn);
            
            $_SESSION['success_message'] = "Product updated successfully.";
            header('Location: products.php');
            exit();

        } catch (Exception $e) {
            // Rollback transaction on error
            mysqli_rollback($conn);

            // Delete uploaded image if it exists
            if (!empty($new_image_name) && file_exists('uploads/products/' . $new_image_name)) {
                unlink('uploads/products/' . $new_image_name);
            }

            $_SESSION['error_message'] = "Error: " . $e->getMessage();
            header('Location: products.php');
            exit();
        }
    } else {
        // Handle validation errors
        $_SESSION['error_message'] = "Validation errors: " . implode(", ", $errors);
        header('Location: products.php');
        exit();
    }
} else {
    // If not POST request, redirect to products page
    header('Location: products.php');
    exit();
}