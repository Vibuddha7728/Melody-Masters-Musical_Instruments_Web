<?php session_start(); include '../includes/db_config.php'; 
$res = $conn->query("SELECT SUM(total_amount) as total FROM orders");
$data = $res->fetch_assoc();
?>
<div class="container mt-4">
    <div class="alert alert-success">
        <h3>Total Revenue: £<?php echo number_format($data['total'], 2); ?></h3>
    </div>
</div>