<?php
session_start();
include 'includes/db_config.php';

// බොත්තම එබූ විට ක්‍රියාත්මක වේ
if (isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['p_id']);
    $quantity = intval($_POST['qty']);

    // Session එකේ cart එකක් නැතිනම් සාදන්න
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // දැනටමත් භාණ්ඩය තිබේ නම් ප්‍රමාණය එකතු කරන්න, නැතිනම් අලුතින් දාන්න
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }

    // සාර්ථකව එකතු වූ පසු cart.php වෙත යොමු කරයි
    header("Location: cart.php");
    exit();
} else {
    header("Location: shop.php");
    exit();
}
?>