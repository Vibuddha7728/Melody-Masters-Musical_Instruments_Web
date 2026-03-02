<?php
session_start();
include 'includes/db_config.php';

if (isset($_POST['id']) && isset($_POST['qty'])) {
    $id = (int)$_POST['id'];
    $qty = (int)$_POST['qty'];

    // 1. Update Session Cart
    if ($qty > 0) {
        $_SESSION['cart'][$id] = $qty;
    } else {
        unset($_SESSION['cart'][$id]);
    }

    // 2. Initialize variables
    $subtotal = 0;
    $line_total = 0;
    $has_physical = false;

    // 3. Calculate Totals and Check Product Types
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $pid => $pqty) {
            $pid = (int)$pid;
            
            // Database එකෙන් අගයන් ලබා ගැනීම
            $res = $conn->query("SELECT price, product_type FROM products WHERE id = $pid");
            
            if ($res && $res->num_rows > 0) {
                $product = $res->fetch_assoc();
                $price = $product['price'];
                
                // Rule: Physical items තිබේදැයි පරීක්ෂා කිරීම
                if ($product['product_type'] === 'physical') {
                    $has_physical = true;
                }

                $current_line_total = $price * $pqty;
                $subtotal += $current_line_total;
                
                // දැනට update කරන item එකේ line total එක ලබා ගැනීම
                if ($pid == $id) {
                    $line_total = number_format($current_line_total, 2);
                }
            }
        }
    }

    // 4. Business Rule: Shipping Logic
    // Physical items තිබේ නම් පමණක් shipping අදාළ වේ.
    // Subtotal එක £100 ට වඩා අඩු නම් £100 ක ගාස්තුවක් අය වේ.
    // Subtotal එක £100 හෝ ඊට වැඩි නම් Shipping Free වේ.
    $shipping = 0;
    if ($has_physical) {
        $shipping = ($subtotal < 100) ? 100 : 0;
    }

    $grand_total = $subtotal + $shipping;

    // 5. Return JSON Response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'line_total' => $line_total,
        'subtotal' => number_format($subtotal, 2),
        'shipping' => ($shipping > 0 ? '£' . number_format($shipping, 2) : 'Free'),
        'total' => number_format($grand_total, 2),
        'has_physical' => $has_physical // ඉදිරි කටයුතු සඳහා මෙයද ඇතුළත් කළා
    ]);
}
?>