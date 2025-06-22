<?php
session_start();
include("db.php");

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Update quantities
if (isset($_POST['qty']) && is_array($_POST['qty'])) {
    foreach ($_POST['qty'] as $id => $qty) {
        $id = (int)$id;
        $qty = (int)$qty;
        if ($qty > 0) {
            $_SESSION['cart'][$id] = $qty;
        }
    }
}

// Remove items
if (isset($_POST['remove']) && is_array($_POST['remove'])) {
    foreach ($_POST['remove'] as $removeId) {
        $removeId = (int)$removeId;
        unset($_SESSION['cart'][$removeId]);
    }
}

header("Location: cart.php");
exit();
