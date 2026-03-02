<?php
session_start();
include '../includes/db_config.php';

// Button එක click කර ඇත්දැයි බැලීම
if (isset($_POST['submit_review'])) {
    
    // User login වී නැත්නම් redirect කිරීම
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../index.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    
    // දත්ත ලබා ගැනීම සහ Security පරීක්ෂාව
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $rating = mysqli_real_escape_string($conn, $_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);

    // මිලදී ගෙන ඇත්දැයි සහ status එක 'completed' ද යන්න පරීක්ෂාව
    $check_purchase = "SELECT o.id FROM orders o 
                       JOIN order_items oi ON o.id = oi.order_id 
                       WHERE o.user_id = '$user_id' 
                       AND oi.product_id = '$product_id' 
                       AND LOWER(o.status) = 'completed' LIMIT 1";
    
    $result = mysqli_query($conn, $check_purchase);

    if ($result && mysqli_num_rows($result) > 0) {
        // Review එක Insert කිරීම
        $sql = "INSERT INTO reviews (user_id, product_id, rating, comment, created_at) 
                VALUES ('$user_id', '$product_id', '$rating', '$comment', NOW())";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Review submitted successfully!'); window.location.href='my_account.php';</script>";
        } else {
            // Database Error එකක් ආවොත්
            echo "<script>alert('Error: Could not save review.'); window.location.href='my_account.php';</script>";
        }
    } else {
        // Order එක Completed නැත්නම් හෝ මිලදී ගෙන නැත්නම්
        echo "<script>alert('You can only review products from completed orders!'); window.location.href='my_account.php';</script>";
    }
} else {
    // Direct access අවහිර කිරීම
    header("Location: my_account.php");
    exit();
}
?>