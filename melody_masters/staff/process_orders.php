<?php
session_start();
include '../includes/db_config.php';

// Staff හෝ Admin පමණක් ඇතුළු වීමට ඉඩ දීම
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'staff' && $_SESSION['role'] != 'admin')) {
    header("Location: ../index.php");
    exit();
}

$message = "";

// Order Status හෝ Tracking Number update කිරීමේ logic එක
if (isset($_POST['update_order'])) {
    $order_id = intval($_POST['order_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $tracking = mysqli_real_escape_string($conn, $_POST['tracking_no']);

    $sql = "UPDATE orders SET status = ?, tracking_no = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $status, $tracking, $order_id);

    if ($stmt->execute()) {
        $message = "Swal.fire({
            icon: 'success',
            title: 'Action Successful',
            text: 'Order #ORD-00$order_id updated to $status status.',
            background: '#121212',
            color: '#ffffff',
            confirmButtonColor: '#ffc107'
        });";
    }
}

// Search Logic
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management | Melody Masters Elite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --accent: #ffc107;
            --bg-deep: #0a0a0a;
            --surface: #161616;
            --border: rgba(255, 255, 255, 0.1);
            --text-main: #ffffff;
            --text-dim: #b0b0b0;
        }

        body {
            background-color: var(--bg-deep);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            letter-spacing: -0.2px;
            overflow-x: hidden;
        }

        /* Header Section */
        .page-header {
            padding: 40px 0 30px;
            background: radial-gradient(circle at top right, rgba(255, 193, 7, 0.08), transparent);
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            color: var(--text-main);
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
        }

        .btn-back:hover {
            background: var(--accent);
            color: #000;
            transform: translateX(-5px);
        }

        /* Search Input Styling */
        .search-container {
            position: relative;
            min-width: 250px;
        }

        .search-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 10px 15px 10px 40px;
            color: #fff;
            width: 100%;
            transition: all 0.3s;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--accent);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent);
            pointer-events: none;
        }

        /* Console Card Styling */
        .console-container {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            margin-bottom: 50px;
        }

        .table {
            color: var(--text-main);
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 900px; /* Ensures table layout stays clean on small screens */
        }

        .table thead th {
            background: rgba(255, 255, 255, 0.02);
            padding: 20px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            color: var(--accent);
            border-bottom: 1px solid var(--border);
        }

        .table tbody td {
            padding: 20px;
            vertical-align: middle;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            background: transparent;
        }

        /* Data Presentation */
        .order-id {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            color: var(--accent);
            font-size: 1.1rem;
        }

        .customer-name {
            font-weight: 700;
            color: #fff;
            font-size: 1rem;
        }

        .customer-email {
            font-size: 0.8rem;
            color: var(--text-dim);
        }

        .logistics-data {
            font-size: 0.85rem;
            color: #e0e0e0;
            line-height: 1.5;
            max-width: 220px;
        }

        /* Custom Status Badges */
        .status-badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .status-pending { background: rgba(255, 193, 7, 0.15); color: #ffc107; border: 1px solid rgba(255, 193, 7, 0.3); }
        .status-shipped { background: rgba(13, 202, 240, 0.15); color: #0dcaf0; border: 1px solid rgba(13, 202, 240, 0.3); }
        .status-completed { background: rgba(25, 135, 84, 0.15); color: #2ecc71; border: 1px solid rgba(25, 135, 84, 0.3); }

        /* Form Interaction */
        .input-dark {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border);
            color: #fff;
            border-radius: 10px;
            padding: 8px 12px;
            font-size: 0.85rem;
            transition: all 0.3s;
        }

        .input-dark:focus {
            background: rgba(0, 0, 0, 0.5);
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.15);
            outline: none;
            color: #fff;
        }

        .btn-update-action {
            background: var(--accent);
            color: #000;
            border: none;
            padding: 8px 18px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            transition: all 0.3s;
        }

        /* Mobile Adjustments */
        @media (max-width: 768px) {
            .page-header { padding: 30px 0; }
            .search-container { margin-right: 0 !important; width: 100%; margin-bottom: 10px; }
            .header-actions { flex-direction: column; width: 100%; }
            .btn-back { width: 100%; justify-content: center; }
            .console-container { border-radius: 15px; }
        }

        /* Empty State */
        .empty-row {
            padding: 60px !important;
            text-align: center;
            color: var(--text-dim);
        }
    </style>
</head>
<body>

<div class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-5 mb-3 mb-md-0">
                <h6 class="text-uppercase fw-bold text-warning mb-2" style="letter-spacing: 2px;">Management Portal</h6>
                <h1 class="fw-800 m-0">Order Console<span class="text-warning">.</span></h1>
            </div>
            <div class="col-lg-6 col-md-7">
                <div class="d-flex flex-column flex-md-row justify-content-md-end align-items-center gap-2">
                    <form action="" method="GET" class="search-container">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" name="search" class="search-input" placeholder="Search by ID, Name..." value="<?php echo htmlspecialchars($search_query); ?>">
                    </form>
                    
                    <a href="staff_dashboard.php" class="btn-back">
                        <i class="bi bi-grid-fill me-2"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="console-container">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Customer</th>
                        <th>Logistics Route</th>
                        <th>Status</th>
                        <th class="text-center">Action Center</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT orders.*, users.username, users.email, users.address 
                              FROM orders 
                              JOIN users ON orders.user_id = users.id";
                    
                    if (!empty($search_query)) {
                        $query .= " WHERE orders.id LIKE '%$search_query%' 
                                    OR users.username LIKE '%$search_query%' 
                                    OR orders.tracking_no LIKE '%$search_query%'";
                    }

                    $query .= " ORDER BY orders.id DESC";
                    
                    $result = $conn->query($query);

                    if ($result && $result->num_rows > 0):
                        while($row = $result->fetch_assoc()):
                            $status_type = strtolower($row['status']);
                            $badge_class = "status-pending";
                            if($status_type == 'shipped') $badge_class = "status-shipped";
                            if($status_type == 'completed') $badge_class = "status-completed";
                    ?>
                    <tr>
                        <td>
                            <div class="order-id">#ORD-<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></div>
                            <div class="fw-bold mt-1" style="color: #fff;">£<?php echo number_format($row['total_amount'], 2); ?></div>
                        </td>
                        <td>
                            <div class="customer-name"><?php echo htmlspecialchars($row['username']); ?></div>
                            <div class="customer-email"><?php echo htmlspecialchars($row['email']); ?></div>
                        </td>
                        <td>
                            <div class="logistics-data">
                                <i class="bi bi-geo-alt-fill text-warning me-1"></i>
                                <?php echo htmlspecialchars($row['address'] ?? 'Address not listed'); ?>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $badge_class; ?>">
                                <i class="bi bi-record-fill"></i> <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" class="d-flex gap-2 justify-content-center px-2">
                                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                
                                <input type="text" name="tracking_no" class="input-dark" style="width: 140px;"
                                       placeholder="Tracking No." value="<?php echo htmlspecialchars($row['tracking_no'] ?? ''); ?>">
                                
                                <select name="status" class="input-dark">
                                    <option value="Pending" <?php if($row['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                    <option value="Processing" <?php if($row['status'] == 'Processing') echo 'selected'; ?>>Processing</option>
                                    <option value="Shipped" <?php if($row['status'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
                                    <option value="Completed" <?php if($row['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                                </select>
                                
                                <button type="submit" name="update_order" class="btn-update-action">
                                    Update
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="5" class="empty-row" style="min-width: 100%;">
                            <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                            <p class="m-0">No orders matching your search were found.</p>
                            <?php if(!empty($search_query)): ?>
                                <a href="?" class="btn btn-sm btn-outline-warning mt-2">Clear Search</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    <?php echo $message; ?>
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>