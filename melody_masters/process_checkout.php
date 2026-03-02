<?php
session_start();
include 'includes/db_config.php'; 

if (isset($_POST['place_order'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Session expired. Please login again.'); window.location='index.php';</script>";
        exit();
    }

    if (empty($_SESSION['cart'])) {
        header("Location: shop.php");
        exit();
    }

    $user_id = (int)$_SESSION['user_id'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // --- පියවර 01: මුළු මුදල (Grand Total) සහ Product Type පරීක්ෂා කිරීම ---
    $subtotal = 0;
    $has_physical = false;
    $cart_data = [];

    foreach ($_SESSION['cart'] as $id => $qty) {
        $id = (int)$id;
        $res = $conn->query("SELECT price, product_type FROM products WHERE id = $id");
        if ($res && $res->num_rows > 0) {
            $product = $res->fetch_assoc();
            $price = $product['price'];
            $subtotal += ($price * $qty);
            
            if (strtolower($product['product_type'] ?? '') == 'physical') { 
                $has_physical = true; 
            }

            // පසුව order_items වලට දැමීම සඳහා දත්ත තබා ගැනීම
            $cart_data[] = [
                'id' => $id,
                'qty' => $qty,
                'price' => $price
            ];
        }
    }

    // Shipping ගණනය කිරීම
    $shipping = ($has_physical && $subtotal < 100) ? 50.00 : 0.00;
    $grand_total = $subtotal + $shipping; 

    // --- Dynamic Status Logic ---
    // Physical product එකක් ඇත්නම් එය 'pending' වේ.
    // Digital පමණක් ඇත්නම් එය 'completed' වේ.
    if ($has_physical) {
        $status = 'pending';
        $alert_msg = "Order Successful! Your physical items are now PENDING.";
    } else {
        $status = 'completed';
        $alert_msg = "Order Successful! Your digital order is COMPLETED.";
    }

    // --- පියවර 02: Order එක ඇතුළත් කිරීම ---
    $sql = "INSERT INTO orders (user_id, total_amount, status, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ids", $user_id, $grand_total, $status);
    
    if ($stmt->execute()) {
        $order_id = $conn->insert_id;

        // --- පියවර 03: Cart එකේ බඩු Order Items වලට දැමීම ---
        foreach ($cart_data as $item) {
            $p_id = $item['id'];
            $p_qty = $item['qty'];
            $p_price = $item['price'];
            
            $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $item_stmt = $conn->prepare($item_sql);
            $item_stmt->bind_param("iiid", $order_id, $p_id, $p_qty, $p_price);
            $item_stmt->execute();
        }

        // --- පියවර 04: Cart එක හිස් කිරීම සහ Redirect කිරීම ---
        unset($_SESSION['cart']);
        
        echo "<script>
                alert('$alert_msg Amount: £" . number_format($grand_total, 2) . "'); 
                window.location='customer/my_account.php';
              </script>";
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    header("Location: shop.php");
    exit();
}
?>