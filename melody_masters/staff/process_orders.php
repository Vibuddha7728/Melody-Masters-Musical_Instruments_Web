<?php
session_start();
include '../includes/db_config.php';

// Staff හෝ Admin පමණක් ඇතුළු වීමට ඉඩ දීම
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'staff' && $_SESSION['role'] != 'admin')) {
    header("Location: ../index.php");
    exit();
}

// SMS යැවීමේ Function එක
function sendOrderSMS($phone, $order_id, $status) {
    // දුරකථන අංකය 947XXXXXXXX ආකාරයට සකස් කිරීම
    $formatted_phone = "94" . ltrim(ltrim($phone, '94'), '0');
    
    // පණිවිඩය සකස් කිරීම
    $sms_msg = "Melody Masters Elite: Your Order #ORD-00$order_id status has been updated to '$status'. Thank you for shopping with us!";

    $api_key = "472|WEvtWFEVbBRFz3aWYSdRiIwf9XuHa51MAp71wgue"; 
    $sender_id = "SMSAPI Demo"; 
    $url = "https://dashboard.smsapi.lk/api/v3/sms/send";
    
    $data = array(
        'recipient' => $formatted_phone,
        'sender_id' => $sender_id,
        'message' => $sms_msg
    );

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer " . $api_key,
        "Content-Type: application/json"
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}

$message = "";

// Order Status හෝ Tracking Number update කිරීමේ logic එක
if (isset($_POST['update_order'])) {
    $order_id = intval($_POST['order_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $tracking = mysqli_real_escape_string($conn, $_POST['tracking_no']);

    // පාරිභෝගිකයාගේ දුරකථන අංකය ලබා ගැනීම
    $user_query = "SELECT users.phone_number FROM orders JOIN users ON orders.user_id = users.id WHERE orders.id = $order_id";
    $user_res = $conn->query($user_query);
    $user_data = $user_res->fetch_assoc();
    $customer_phone = $user_data['phone_number'] ?? '';

    $sql = "UPDATE orders SET status = ?, tracking_no = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $status, $tracking, $order_id);

    if ($stmt->execute()) {
        // Update එක සාර්ථක නම් පමණක් SMS එක යවන්න
        if (!empty($customer_phone)) {
            sendOrderSMS($customer_phone, $order_id, $status);
        }

        $message = "Swal.fire({
            icon: 'success',
            title: 'Action Successful',
            text: 'Order #ORD-00$order_id updated to $status status and SMS sent.',
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
            --danger: #ff4d4d; 
        }

        body {
            background-color: var(--bg-deep);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            letter-spacing: -0.2px;
            overflow-x: hidden;
        }

        .page-header { padding: 40px 0 30px; background: radial-gradient(circle at top right, rgba(255, 193, 7, 0.08), transparent); }
        .btn-back { background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border); color: var(--text-main); padding: 10px 20px; border-radius: 12px; font-weight: 600; text-decoration: none; transition: all 0.3s ease; display: inline-flex; align-items: center; }
        .btn-back:hover { background: var(--accent); color: #000; transform: translateX(-5px); }
        
        .search-container { position: relative; min-width: 250px; }
        .search-input { background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border); border-radius: 12px; padding: 10px 15px 10px 40px; color: #fff; width: 100%; transition: all 0.3s; }
        .search-input:focus { outline: none; border-color: var(--accent); background: rgba(255, 255, 255, 0.1); box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.1); }
        .search-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--accent); pointer-events: none; }

        .console-container { background: var(--surface); border: 1px solid var(--border); border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); overflow: hidden; margin-bottom: 50px; }
        .table { color: var(--text-main); margin-bottom: 0; min-width: 1000px; border-collapse: separate; border-spacing: 0; }
        
        .table thead th { 
            background: rgba(255, 255, 255, 0.02); 
            padding: 20px; 
            font-size: 0.75rem; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            color: var(--text-dim); 
            border-bottom: 1px solid var(--border); 
            text-align: left;
        }

        .table tbody tr { transition: background 0.2s ease; }
        .table tbody tr:hover { background: rgba(255, 255, 255, 0.03); }
        
        .table tbody td { padding: 20px; vertical-align: middle; border-bottom: 1px solid rgba(255, 255, 255, 0.05); background: transparent; }

        .order-id { font-family: 'Courier New', monospace; font-weight: 700; color: var(--accent); font-size: 1.1rem; cursor: pointer; text-decoration: underline dotted; transition: 0.2s; }
        .order-id:hover { color: #fff; }
        .customer-name { font-weight: 700; color: #fff; }
        .customer-email { font-size: 0.8rem; color: var(--text-dim); }
        
        .contact-logistics-box { display: flex; flex-direction: column; gap: 5px; }
        .logistics-data { color: #e0e0e0; font-size: 0.85rem; max-width: 250px; line-height: 1.4; }
        .phone-data { color: var(--accent); font-weight: 600; font-size: 0.9rem; }

        .payment-method-badge { 
            font-size: 0.75rem; 
            font-weight: 600; 
            padding: 4px 10px; 
            border-radius: 6px; 
            display: inline-flex; 
            align-items: center; 
            gap: 5px; 
            margin: 4px 0; 
            border-width: 1px;
            border-style: solid;
        }

        .pm-card {
            color: #0dcaf0;
            background: rgba(13, 202, 240, 0.1);
            border-color: rgba(13, 202, 240, 0.2);
        }

        .pm-cod {
            color: var(--danger);
            background: rgba(255, 77, 77, 0.1);
            border-color: rgba(255, 77, 77, 0.2);
        }

        .status-badge { padding: 6px 12px; border-radius: 8px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; display: inline-flex; align-items: center; gap: 6px; }
        .status-pending { background: rgba(255, 193, 7, 0.15); color: #ffc107; border: 1px solid rgba(255, 193, 7, 0.3); }
        .status-shipped { background: rgba(13, 202, 240, 0.15); color: #0dcaf0; border: 1px solid rgba(13, 202, 240, 0.3); }
        .status-completed { background: rgba(25, 135, 84, 0.15); color: #2ecc71; border: 1px solid rgba(25, 135, 84, 0.3); }

        .input-dark { background: rgba(0, 0, 0, 0.3); border: 1px solid var(--border); color: #fff; border-radius: 10px; padding: 8px 12px; font-size: 0.85rem; }
        .btn-update-action { background: var(--accent); color: #000; border: none; padding: 8px 18px; border-radius: 10px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; transition: 0.3s; }
        .btn-update-action:hover { opacity: 0.8; transform: translateY(-2px); }

        .tooltip-inner {
            background-color: #000000 !important;
            opacity: 1 !important; 
            border: 2px solid var(--accent);
            padding: 18px !important;
            max-width: 450px !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 1);
            border-radius: 12px;
            text-align: left;
            color: #ffffff !important;
        }
        
        .tt-title { color: var(--accent); font-size: 0.9rem; font-weight: 800; margin-bottom: 12px; text-transform: uppercase; border-bottom: 1px solid rgba(255,193,7,0.5); padding-bottom: 6px; display: block; }
        .tt-table { margin: 0; width: 100%; font-size: 0.85rem; color: #fff; border-collapse: collapse; }
        .tt-table th { color: #aaa; padding: 5px 8px; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .tt-table td { padding: 12px 8px; border-bottom: 1px solid rgba(255,255,255,0.08); vertical-align: middle; }

        .btn-print-label { background: rgba(255, 255, 255, 0.05); color: #fff; border: 1px solid var(--border); padding: 8px 12px; border-radius: 10px; font-weight: 600; font-size: 0.8rem; text-decoration: none; transition: 0.3s; display: inline-flex; align-items: center; }
        .btn-print-label:hover { background: #fff; color: #000; }
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
                        <th>Contact & Logistics</th>
                        <th>Status</th>
                        <th class="text-center">Action Center</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT orders.*, users.username, users.email, users.address, users.phone_number 
                              FROM orders 
                              JOIN users ON orders.user_id = users.id";
                    
                    if (!empty($search_query)) {
                        $query .= " WHERE orders.id LIKE '%$search_query%' 
                                    OR users.username LIKE '%$search_query%' 
                                    OR users.phone_number LIKE '%$search_query%'
                                    OR orders.tracking_no LIKE '%$search_query%'";
                    }

                    $query .= " ORDER BY orders.id DESC";
                    $result = $conn->query($query);

                    if ($result && $result->num_rows > 0):
                        while($row = $result->fetch_assoc()):
                            $order_id = $row['id'];
                            
                            $items_query = "SELECT order_items.quantity, order_items.price, products.name 
                                            FROM order_items 
                                            JOIN products ON order_items.product_id = products.id 
                                            WHERE order_items.order_id = $order_id";
                            $items_res = $conn->query($items_query);
                            
                            $tooltip_html = "<span class='tt-title'><i class='bi bi-bag-check-fill me-2'></i>Order Contents</span>";
                            $tooltip_html .= "<table class='tt-table'><thead><tr><th>Product Name</th><th>Qty</th><th>Price</th></tr></thead><tbody>";
                            while($item = $items_res->fetch_assoc()){
                                $tooltip_html .= "<tr><td class='tt-item-name'>".htmlspecialchars($item['name'])."</td><td class='tt-item-qty'>x".$item['quantity']."</td><td class='tt-item-price'>£".number_format($item['price'], 2)."</td></tr>";
                            }
                            $tooltip_html .= "</tbody></table>";

                            $status_type = strtolower($row['status']);
                            $badge_class = "status-pending";
                            if($status_type == 'shipped') $badge_class = "status-shipped";
                            if($status_type == 'completed') $badge_class = "status-completed";

                            $pm = $row['payment_method'] ?? 'N/A';
                            if (stripos($pm, 'Card') !== false) {
                                $pm_class = "pm-card";
                                $pm_icon = "bi-credit-card";
                            } else {
                                $pm_class = "pm-cod"; 
                                $pm_icon = "bi-cash-stack";
                            }
                    ?>
                    <tr>
                        <td>
                            <div class="order-id" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-html="true" title="<?php echo htmlspecialchars($tooltip_html); ?>">
                                #ORD-<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?>
                            </div>
                            <div class="fw-bold mt-1" style="color: #fff;">£<?php echo number_format($row['total_amount'], 2); ?></div>
                        </td>
                        <td>
                            <div class="customer-name"><?php echo htmlspecialchars($row['username']); ?></div>
                            <div class="customer-email"><?php echo htmlspecialchars($row['email']); ?></div>
                        </td>
                        <td>
                            <div class="contact-logistics-box">
                                <div class="phone-data">
                                    <i class="bi bi-telephone-fill me-1"></i>
                                    <?php echo htmlspecialchars($row['phone_number'] ?? 'N/A'); ?>
                                </div>
                                
                                <div>
                                    <span class="payment-method-badge <?php echo $pm_class; ?>">
                                        <i class="bi <?php echo $pm_icon; ?>"></i>
                                        <?php echo htmlspecialchars($pm); ?>
                                    </span>
                                </div>

                                <div class="logistics-data">
                                    <i class="bi bi-geo-alt-fill text-warning me-1"></i>
                                    <?php echo htmlspecialchars($row['address'] ?? 'Address not listed'); ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $badge_class; ?>">
                                <i class="bi bi-record-fill"></i> <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-column align-items-center gap-2">
                                <form method="POST" class="d-flex gap-2 justify-content-center px-2">
                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                    <input type="text" name="tracking_no" class="input-dark" style="width: 140px;" placeholder="Tracking No." value="<?php echo htmlspecialchars($row['tracking_no'] ?? ''); ?>">
                                    <select name="status" class="input-dark">
                                        <option value="Pending" <?php if($row['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                        <option value="Processing" <?php if($row['status'] == 'Processing') echo 'selected'; ?>>Processing</option>
                                        <option value="Shipped" <?php if($row['status'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
                                        <option value="Completed" <?php if($row['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                                    </select>
                                    <button type="submit" name="update_order" class="btn-update-action">Update</button>
                                </form>
                                <a href="print_label.php?id=<?php echo $row['id']; ?>" target="_blank" class="btn-print-label">
                                    <i class="bi bi-printer me-2"></i>Print Label
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="5" class="empty-row text-center py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                            <p class="m-0">No orders matching your search were found.</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    <?php echo $message; ?>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            sanitize: false,
            animation: false,
            trigger: 'hover'
        })
    })
</script>
</body>
</html>