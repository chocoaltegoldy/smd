<?php
include 'db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "Product ID is missing!";
    exit;
}

$result = $conn->query("SELECT * FROM products WHERE id = $id");
$product = $result->fetch_assoc();
$categories = $conn->query("SELECT * FROM categories");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $image = $_POST['image'];

    $stmt = $conn->prepare("UPDATE products SET name=?, category_id=?, image=? WHERE id=?");
    $stmt->bind_param("sisi", $name, $category, $image, $id);
    $stmt->execute();
    header("Location: admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head><title>Edit Product</title><link rel="stylesheet" href="css/style.css" /></head>
<body>
<h2>Edit Product</h2>
<form method="POST">
    <label>Name:</label><br>
    <input type="text" name="name" value="<?= $product['name'] ?>" required><br><br>
    <label>Category:</label><br>
    <select name="category" required>
        <?php while ($row = $categories->fetch_assoc()): ?>
        <option value="<?= $row['id'] ?>" <?= $product['category_id'] == $row['id'] ? 'selected' : '' ?>>
            <?= $row['name'] ?>
        </option>
        <?php endwhile; ?>
    </select><br><br>
    <label>Image URL:</label><br>
    <input type="text" name="image" value="<?= $product['image'] ?>" required><br><br>
    <button type="submit">Update</button>
</form>
</body>
</html>