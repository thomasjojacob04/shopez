<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "ecommerce";

    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Validate required fields
    if (empty($_POST['name']) || empty($_POST['price']) || empty($_POST['description'])) {
        die("All fields are required");
    }

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = (float)$_POST['price'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $file_name = uniqid() . '_' . $_FILES['image']['name'];
            $upload_path = 'uploads/products/' . $file_name;
            
            // Create directory if it doesn't exist
            if (!file_exists('uploads/products')) {
                mkdir('uploads/products', 0777, true);
            }
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image = $file_name;
            } else {
                die("Error uploading file");
            }
        } else {
            die("Invalid file type");
        }
    } else {
        die("Image is required");
    }

    $query = "INSERT INTO products (name, price, image, description, created_at) 
              VALUES ('$name', $price, '$image', '$description', NOW())";

    if (mysqli_query($conn, $query)) {
        header('Location: products.php');
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>