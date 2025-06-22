<?php
session_start();
require_once 'db.php';

$timeoutDuration = 60;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeoutDuration) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// AJAX: return products by category
if (isset($_GET['ajax_category'])) {
    $filter = $_GET['ajax_category'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE category = ?");
    $stmt->bind_param("s", $filter);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = [];
    while ($row = $result->fetch_assoc()) $products[] = $row;
    echo json_encode($products);
    exit();
}

// AJAX: return all products
if (isset($_GET['ajax_all'])) {
    $result = $conn->query("SELECT * FROM products");
    $products = [];
    while ($row = $result->fetch_assoc()) $products[] = $row;
    echo json_encode($products);
    exit();
}

// Add/Edit/Delete
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name']);
    $description = sanitize_input($_POST['description']);
    $category = sanitize_input($_POST['category']);
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
    $imagePath = '';

    if (!empty($_FILES['image']['name']) && in_array($_FILES['image']['type'], $allowedTypes)) {
        $targetDir = 'images/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $targetFile = $targetDir . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $targetFile;
        }
    } elseif (!empty($_POST['image_url'])) {
        $imagePath = sanitize_input($_POST['image_url']);
    }

    if ($name && $description && $category && ($imagePath || isset($_POST['existing_image']))) {
        if (!empty($_POST['edit_id'])) {
            $edit_id = (int)$_POST['edit_id'];
            $imageToSave = $imagePath ?: $_POST['existing_image'];
            $stmt = $conn->prepare("UPDATE products SET name=?, description=?, category=?, image=?, price=? WHERE id=?");
            $stmt->bind_param("ssssdi", $name, $description, $category, $imageToSave, $price, $edit_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO products (name, description, category, image, price) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssd", $name, $description, $category, $imagePath, $price);
        }

        if ($stmt->execute()) {
            $_SESSION['message'] = isset($edit_id) ? "Product updated!" : "Product added!";
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
        }
        $stmt->close();
        header('Location: admin.php');
        exit();
    } else {
        $_SESSION['message'] = "All fields are required.";
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $_SESSION['message'] = "Product deleted.";
    header('Location: admin.php');
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$categories = [];
$res = $conn->query("SELECT DISTINCT category FROM products");
while ($row = $res->fetch_assoc()) {
    $categories[] = $row['category'];
}

$editProduct = null;
$edit_id = '';
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $editProduct = $result->fetch_assoc();
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Panel - SMD MEDICARE</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="css/style.css" />
  <style>
    body { font-family: 'Poppins', sans-serif; margin: 0; padding: 0; background-color: #f5f7fa; }
    .navbar { background: white; padding: 10px 20px; border-bottom: 3px solid #0072bc; display: flex; justify-content: space-between; align-items: center; }
    .navbar img { height: 50px; }
    .navbar ul { list-style: none; display: flex; gap: 20px; margin: 0; padding: 0; }
    .navbar ul li a { color: #0072bc; text-decoration: none; font-weight: bold; }
    .navbar ul li a:hover, .navbar ul li a.active { color: #3aaa35; }

    .hero { background: linear-gradient(to right, #0072bc, #3aaa35); color: white; padding: 50px 20px 60px; text-align: center; }

    .toast { background: #00c853; color: white; padding: 12px; text-align: center; margin: 10px auto; max-width: 600px; border-radius: 6px; }

    .horizontal-form-wrapper {
      display: flex;
      justify-content: center;
      margin-top: 40px;
    }

    .add-form {
      background: linear-gradient(to right, #f9f9f9, #e3f2fd);
      padding: 30px;
      border-radius: 12px;
      max-width: 600px;
      width: 100%;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .add-form input, .add-form textarea, .add-form select {
      width: 100%;
      margin-bottom: 15px;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
    }

    .add-form button {
      background: #0072bc;
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 8px;
      font-size: 1em;
      cursor: pointer;
    }

    .add-form button:hover { background: #3aaa35; }

    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px; padding: 30px; }
    .product-card { background: white; padding: 15px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    .product-card img { width: 100%; height: 160px; object-fit: contain; border-radius: 8px; }
    .product-card h3 { margin: 10px 0 5px; }
    .product-card .actions a { margin-right: 5px; padding: 6px 12px; color: white; text-decoration: none; border-radius: 5px; }
    .edit-btn { background: #0072bc; }
    .delete-btn { background: #e53935; }

    .filter-bar { text-align: center; margin: 30px auto; }
    .filter-bar a {
      margin: 5px;
      padding: 8px 16px;
      background: #0072bc;
      color: white;
      border-radius: 20px;
      text-decoration: none;
      display: inline-block;
      cursor: pointer;
    }
    .filter-bar a.active, .filter-bar a:hover { background: #3aaa35; }
  </style>
</head>
<body>

<header class="navbar">
  <div class="logo">
    <a href="index.php"><img src="images/SMD MEDICARE.jpeg" alt="SMD Logo"></a>
  </div>
  <nav>
    <ul>
      <li><a href="index.php">Home</a></li>
      <li><a href="admin.php" class="active">Admin</a></li>
      <li><a href="?logout=1" style="color: red;">Logout</a></li>
    </ul>
  </nav>
</header>

<section class="hero">
  <h1>Admin Panel</h1>
  <p>Manage your product listings securely.</p>

  <div class="horizontal-form-wrapper">
    <form class="add-form" method="post" enctype="multipart/form-data">
      <input type="hidden" name="edit_id" value="<?= $edit_id ?>">
      <input type="hidden" name="existing_image" value="<?= htmlspecialchars($editProduct['image'] ?? '') ?>">
      <input type="text" name="name" placeholder="Product Name" required value="<?= htmlspecialchars($editProduct['name'] ?? '') ?>">
      <textarea name="description" placeholder="Description" required><?= htmlspecialchars($editProduct['description'] ?? '') ?></textarea>
      <select name="category" required>
        <option value="">-- Select Category --</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat ?>" <?= (isset($editProduct['category']) && $editProduct['category'] == $cat) ? 'selected' : '' ?>><?= $cat ?></option>
        <?php endforeach; ?>
      </select>
      <input type="file" name="image" accept="image/*">
      <input type="text" name="image_url" placeholder="Or enter image URL">
      <input type="text" name="price" placeholder="Price (₹)" value="<?= htmlspecialchars($editProduct['price'] ?? '') ?>">
      <button type="submit"><?= $editProduct ? "Update Product" : "Add Product" ?></button>
    </form>
  </div>
</section>

<?php if (isset($_SESSION['message'])): ?>
  <div class="toast"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
<?php endif; ?>

<div class="filter-bar">
  <a onclick="loadAll()" class="active" id="filter-all">All</a>
  <?php foreach ($categories as $cat): ?>
    <a onclick="filterCategory('<?= addslashes($cat) ?>')" id="filter-<?= md5($cat) ?>"><?= $cat ?></a>
  <?php endforeach; ?>
</div>

<section class="product-grid" id="product-grid"></section>

<script>
  const grid = document.getElementById('product-grid');
  const filterLinks = document.querySelectorAll('.filter-bar a');

  function render(products) {
    grid.innerHTML = '';
    products.forEach(product => {
      grid.innerHTML += `
        <div class="product-card">
          <img src="${product.image}" alt="${product.name}" />
          <h3>${product.name}</h3>
          <p>${product.description}</p>
          <p><strong>₹${parseFloat(product.price).toFixed(2)}</strong></p>
          <div class="actions">
            <a href="?edit=${product.id}" class="edit-btn">Edit</a>
            <a href="?delete=${product.id}" class="delete-btn" onclick="return confirm('Delete this product?');">Delete</a>
          </div>
        </div>
      `;
    });
  }

  function filterCategory(cat) {
    fetch(`admin.php?ajax_category=${encodeURIComponent(cat)}`)
      .then(res => res.json())
      .then(data => {
        render(data);
        updateActive(cat);
      });
  }

  function loadAll() {
    fetch(`admin.php?ajax_all=1`)
      .then(res => res.json())
      .then(data => {
        render(data);
        updateActive('');
      });
  }

  function updateActive(selected) {
    filterLinks.forEach(link => link.classList.remove('active'));
    if (!selected) {
      document.getElementById('filter-all').classList.add('active');
    } else {
      document.querySelector(`#filter-${md5(selected)}`).classList.add('active');
    }
  }

  function md5(string) {
    return string.split('').reduce((a,b) => ((a<<5)-a)+b.charCodeAt(0),0);
  }

  window.onload = loadAll;
</script>

</body>
</html>
