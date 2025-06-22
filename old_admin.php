<?php 
session_start();

$timeoutDuration = 300;
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

$productsFile = 'products.json';
$products = file_exists($productsFile) ? json_decode(file_get_contents($productsFile), true) : [];

function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name']);
    $description = sanitize_input($_POST['description']);
    $category = sanitize_input($_POST['category']);
    $imagePath = '';

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    if (!empty($_FILES['image']['name']) && in_array($_FILES['image']['type'], $allowedTypes)) {
        $targetDir = 'images/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $targetFile = $targetDir . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $targetFile;
        }
    } elseif (!empty($_POST['image_url'])) {
        $imagePath = sanitize_input($_POST['image_url']);
    }

    if ($name && $description && $category && ($imagePath || isset($_POST['existing_image']))) {
        if (isset($_POST['edit_index']) && $_POST['edit_index'] !== '') {
            // Edit product
            $index = (int)$_POST['edit_index'];
            $products[$index]['name'] = $name;
            $products[$index]['description'] = $description;
            $products[$index]['category'] = $category;
            $products[$index]['image'] = $imagePath ? $imagePath : $_POST['existing_image'];
            $_SESSION['message'] = "Product updated successfully!";
        } else {
            // Add product
            $products[] = [
                'name' => $name,
                'description' => $description,
                'category' => $category,
                'image' => $imagePath
            ];
            $_SESSION['message'] = "Product added successfully!";
        }
        file_put_contents($productsFile, json_encode($products, JSON_PRETTY_PRINT));
        header('Location: admin.php');
        exit();
    } else {
        $_SESSION['message'] = "All fields are required.";
    }
}

if (isset($_GET['delete'])) {
    $index = (int)$_GET['delete'];
    if (isset($products[$index])) {
        array_splice($products, $index, 1);
        file_put_contents($productsFile, json_encode($products, JSON_PRETTY_PRINT));
        $_SESSION['message'] = "Product deleted successfully.";
    }
    header('Location: admin.php');
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}

$categoryColors = [
    "Raw Materials" => "#00695c",
    "Uncut Sheet" => "#0072bc",
    "Rapid Test Kits" => "#0072bc",
    "ELISA Kits" => "#3aaa35",
    "Reagents" => "#f39c12",
    "Medical Equipment" => "#e91e63",
    "Positive Human Serum Samples" => "#9c27b0"
];

$editProduct = null;
$editIndex = '';
if (isset($_GET['edit'])) {
    $editIndex = (int)$_GET['edit'];
    if (isset($products[$editIndex])) {
        $editProduct = $products[$editIndex];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Panel - SMD MEDICARE</title>
  <link rel="stylesheet" href="css/style.css" />
  <style>
  body {
    font-family: 'Poppins', sans-serif;
    background-color: #f4f8fb;
    margin: 0;
    padding: 0;
  }

  .toast {
    background: #00c853;
    color: white;
    padding: 10px;
    text-align: center;
    border-radius: 8px;
    margin: 20px auto;
    width: 80%;
    max-width: 500px;
    font-weight: bold;
  }

  .toast.error {
    background: #e53935;
  }

  .product-card .category-tag {
    padding: 5px 10px;
    border-radius: 5px;
    font-weight: bold;
    display: inline-block;
    margin-bottom: 10px;
    color: white;
  }

  #preview {
    max-width: 120px;
    margin: 10px auto;
    display: block;
    border-radius: 8px;
    border: 1px solid #ccc;
  }

  .edit-btn {
    display: inline-block;
    background-color: #1976d2;
    color: #fff;
    padding: 5px 10px;
    text-decoration: none;
    border-radius: 4px;
    margin-top: 5px;
    margin-right: 5px;
  }

  .delete-btn {
    display: inline-block;
    background-color: #e53935;
    color: #fff;
    padding: 5px 10px;
    text-decoration: none;
    border-radius: 4px;
    margin-top: 5px;
  }

  .add-form {
    max-width: 600px;
    margin: 20px auto;
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
  }

  .add-form label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
  }

  .add-form input[type="text"],
  .add-form textarea,
  .add-form select,
  .add-form input[type="file"] {
    width: 100%;
    padding: 10px 12px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-family: inherit;
  }

  .add-form textarea {
    min-height: 100px;
    resize: vertical;
  }

  .add-form button {
    background-color: #00695c;
    color: #fff;
    border: none;
    padding: 12px 20px;
    font-size: 16px;
    border-radius: 6px;
    cursor: pointer;
    width: 100%;
  }

  .add-form button:hover {
    background-color: #004d40;
  }

  .add-form p {
    text-align: center;
    font-weight: bold;
    margin: 15px 0;
  }

  .product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    padding: 20px;
  }

  .product-card {
    background: white;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  .product-card img {
    width: 100%;
    height: 180px; /* fix height for alignment */
    object-fit: cover; /* crop image but fill space */
    border-radius: 8px;
    margin-bottom: 10px;
  }
</style>

</head>
<body>

<header class="navbar">
  <div class="logo">
    <a href="index.php">
      <img src="images/SMD MEDICARE.jpeg" alt="SMD MEDICARE Logo" />
    </a>
  </div>
  <nav>
    <ul>
      <li><a href="index.php">Home</a></li>
      <li><a href="products.php">Products</a></li>
      <li><a href="admin.php" class="active">Admin</a></li>
      <li><a href="contact.php">Contact</a></li>
      <li><a href="?logout=1" onclick="return confirm('Are you sure you want to logout?');">Logout</a></li>
    </ul>
  </nav>
</header>

<section class="hero-section">
  <h1>Our Product Range</h1>
  <p>Explore Diagnostic Kits and Medical Equipment</p>
</section>

<?php if (isset($_SESSION['message'])): ?>
  <div class="toast"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
<?php endif; ?>

<section style="padding: 20px;">
  <h1 style="text-align:center;"><?php echo $editProduct ? 'Edit Product' : 'Admin Panel - Add Product'; ?></h1>
  <form class="add-form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="edit_index" value="<?php echo $editIndex; ?>">
    <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($editProduct['image'] ?? '') ?>">

    <label>Product Name:</label>
    <input type="text" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? $editProduct['name'] ?? '') ?>" />

    <label>Description:</label>
    <textarea name="description" required><?php echo htmlspecialchars($_POST['description'] ?? $editProduct['description'] ?? '') ?></textarea>

    <label>Category:</label>
    <select name="category" required>
      <option value="">-- Select Category --</option>
      <?php
      foreach ($categoryColors as $cat => $color) {
        $selected = ((isset($_POST['category']) && $_POST['category'] == $cat) || (isset($editProduct['category']) && $editProduct['category'] == $cat)) ? "selected" : "";
        echo "<option value=\"$cat\" $selected>$cat</option>";
      }
      ?>
    </select>

    <label>Upload Image:</label>
    <input type="file" name="image" accept="image/*" onchange="previewImage(event)" />
    <img id="preview" src="<?php echo htmlspecialchars($editProduct['image'] ?? '') ?>" />

    <p>OR</p>

    <label>Image URL:</label>
    <input type="text" name="image_url" placeholder="http://example.com/image.jpg" value="<?php echo htmlspecialchars($_POST['image_url'] ?? '') ?>" />

    <button type="submit" name="add_product"><?php echo $editProduct ? 'Update Product' : 'Add Product'; ?></button>
  </form>
</section>

<section style="padding: 20px;">
  <h2 style="text-align:center;">Existing Products</h2>
  <div class="product-grid">
    <?php foreach ($products as $index => $product): 
      $color = $categoryColors[$product['category']] ?? "#00bcd4";
    ?>
      <div class="product-card">
        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" />
        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
        <p class="category-tag" style="background-color: <?php echo $color; ?>;"><?php echo htmlspecialchars($product['category']); ?></p>
        <p><?php echo htmlspecialchars($product['description']); ?></p>
        <a href="?edit=<?php echo $index; ?>" class="edit-btn">Edit</a>
        <a href="?delete=<?php echo $index; ?>" class="delete-btn" onclick="return confirm('Delete this product?');">Delete</a>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Footer -->
<footer>
  <div style="max-width: 1200px; margin: auto; display: flex; flex-wrap: wrap; justify-content: space-between; gap: 20px; text-align: center;">
    <div style="flex: 1; min-width: 260px;">
      <strong>About Us</strong><br>
      SMD MEDICARE – Specializing in raw materials (monoclonal antibodies & antigens), rapid test kits, positive human serum, ELISA kits, biochemistry reagents & medical equipment — trusted partners in diagnostics for 10+ years.
    </div>
    <div style="flex: 1; min-width: 150px;">
      <strong>Quick Links</strong><br>
      <a href="index.php" style="color: black;">Home</a><br>
      <a href="products.php" style="color: black;">Products</a><br>
      <a href="contact.php" style="color: black;">Contact</a>
    </div>
    <div style="flex: 1; min-width: 260px;">
      <strong>Contact Info</strong><br>
      <strong>Hours:</strong> Monday–Saturday, 9:00 AM – 6:00 PM<br>
      <strong>Address:</strong> Shakumbari Vihar, Phase 2, Ganeshpur, Roorkee,<br> Haridwar – 247667<br>
      <strong>Phone:</strong> <a href="tel:+919555422455" style="color: black;">+91 95554 22455</a><br>
      <strong>Email:</strong> <a href="mailto:rahul@smdmedicare.com" style="color: black;">rahul@smdmedicare.com</a>
    </div>
  </div>
  <div style="text-align: center; padding-top: 20px; font-weight: bold;">
    &copy; <?= date("Y") ?> SMD MEDICARE | All Rights Reserved
  </div>
</footer>

<script>
  function previewImage(event) {
    const output = document.getElementById('preview');
    output.src = URL.createObjectURL(event.target.files[0]);
  }
</script>
<script>
  // Extract unique categories from product cards
  const categories = [...new Set(
    Array.from(document.querySelectorAll('.product-card .category-tag')).map(tag => tag.textContent.trim())
  )];

  // Create filter buttons
  const filterContainer = document.createElement('div');
  filterContainer.style.textAlign = 'center';
  filterContainer.style.marginBottom = '20px';

  // Style helper
  const buttonBaseStyle = {
    margin: '0 5px',
    padding: '10px 20px',
    borderRadius: '6px',
    border: 'none',
    cursor: 'pointer',
    color: 'white',
  };

  let activeBtn = null;

  function setActive(btn) {
    if (activeBtn) activeBtn.style.backgroundColor = activeBtn.dataset.originalColor;
    activeBtn = btn;
    btn.dataset.originalColor = btn.style.backgroundColor;
    btn.style.backgroundColor = '#004080';
  }

  // "All" button
  const allBtn = document.createElement('button');
  allBtn.textContent = 'All';
  Object.assign(allBtn.style, buttonBaseStyle, { backgroundColor: '#333' });
  allBtn.onclick = () => {
    document.querySelectorAll('.product-card').forEach(card => card.style.display = 'block');
    setActive(allBtn);
  };
  filterContainer.appendChild(allBtn);
  setActive(allBtn); // default active

  // Category buttons
  categories.forEach(cat => {
    const btn = document.createElement('button');
    btn.textContent = cat;
    Object.assign(btn.style, buttonBaseStyle, { backgroundColor: '#0072bc' });
    btn.onclick = () => {
      document.querySelectorAll('.product-card').forEach(card => {
        const cardCat = card.querySelector('.category-tag').textContent.trim();
        card.style.display = cardCat === cat ? 'block' : 'none';
      });
      setActive(btn);
    };
    filterContainer.appendChild(btn);
  });

  // Insert the filter container above the product grid
  const gridSection = document.querySelector('.product-grid');
  gridSection.parentNode.insertBefore(filterContainer, gridSection);
</script>

</body>
</html>
