<?php
/**
 * Melody Masters - Helper Functions
 */

// 1. භාණ්ඩවල මුළු එකතුව අනුව Shipping ගාස්තුව ගණනය කිරීම
// Business Rule: £100 ට වැඩි නම් Free Shipping, නැතිනම් £10.00
function calculateShipping($subtotal) {
    if ($subtotal > 100) {
        return 0.00; 
    } else {
        return 10.00; 
    }
}

// 2. භාණ්ඩයක් Digital ද නැද්ද යන්න පරීක්ෂා කිරීම
function isDigital($product_id, $conn) {
    $sql = "SELECT product_type FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($product = $result->fetch_assoc()) {
        return ($product['product_type'] == 'digital');
    }
    return false;
}

// 3. භාණ්ඩයක පවතින තොග ප්‍රමාණය (Stock) පරීක්ෂා කිරීම
function checkStock($product_id, $conn) {
    $sql = "SELECT stock FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    return $product['stock'];
}

// 4. පාරිභෝගිකයෙකු ඇත්තටම භාණ්ඩය මිලදී ගෙන ඇත්දැයි බැලීම (For Reviews)
// Business Rule: Customers only allowed to review products they purchased
function hasPurchased($user_id, $product_id, $conn) {
    $sql = "SELECT oi.id FROM order_items oi 
            JOIN orders o ON oi.order_id = o.id 
            WHERE o.user_id = ? AND oi.product_id = ? AND o.status = 'Completed'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return ($result->num_rows > 0);
}

// 5. මුදල් පෙන්වන ආකාරය සකස් කිරීම (Formatting Currency)
function formatPrice($amount) {
    return "£" . number_format($amount, 2);
}
?>