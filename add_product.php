<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $image = $_POST['image'];
    $stmt = $conn->prepare("INSERT INTO products (name, category_id, image) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $name, $category, $image);
    $stmt->execute();
    header("Location: admin.php");
    exit;
}
?>