<?php
session_start();
include("db.php");

$q = trim($_GET['q'] ?? '');
$products = [];

if ($q !== '') {
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE CONCAT('%', ?, '%') OR description LIKE CONCAT('%', ?, '%')");
    $stmt->bind_param("ss", $q, $q);
    $stmt->execute();
    $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$cartCount = array_sum($_SESSION['cart'] ?? []);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Search - SMD Medicare</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<?php include("header.php"); ?>

<section class="hero">
  <h1>Search Results for: <?= htmlspecialchars($q) ?></h1>
</section>

<section class="featured-products">
  <?php if (empty($products)): ?>
    <p style="text-align:center;">No products found matching your search.</p>
  <?php else: ?>
    <div class="product-grid">
      <?php foreach ($products as $product): ?>
        <div class="product-card">
          <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
          <h3><?= htmlspecialchars($product['name']) ?></h3>
          <p><?= htmlspecialchars($product['description']) ?></p>
          <p><strong>₹<?= number_format($product['price'], 2) ?></strong></p>
          <a href="products.php?action=add&product_id=<?= $product['id'] ?>" class="enquiry-button">Add To Cart</a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php include("footer.php"); ?>
</body>
</html>
