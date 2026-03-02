<?php
// Session එක දැනටමත් පටන්ගෙන නැත්නම් පමණක් ආරම්භ කරන්න
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * පරිශීලකයාට අදාළ පිටුවට ඇතුළු වීමට අවසර ඇත්දැයි පරීක්ෂා කිරීම
 * @param string $requiredRole - 'admin', 'staff', හෝ 'customer'
 */
function checkAccess($requiredRole) {
    // 1. පරිශීලකයා Login වී නැත්නම් මුල් පිටුවට යවන්න
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../index.php?error=unauthorized");
        exit();
    }

    // 2. පරිශීලකයාගේ Role එක අවශ්‍ය Role එකට වඩා වෙනස් නම් මුල් පිටුවට යවන්න
    // (Admin කෙනෙකුට Staff පිටුවලට යාමට ඉඩ දීම මෙහිදී සිදු කර ඇත)
    if ($_SESSION['role'] !== $requiredRole) {
        
        // විශේෂ අවස්ථාව: Admin හට Staff පිටුවලට යාමට අවසර දීම
        if ($requiredRole === 'staff' && $_SESSION['role'] === 'admin') {
            return true;
        }

        header("Location: ../index.php?error=access_denied");
        exit();
    }
}

/**
 * පරිශීලකයා Login වී ඇත්දැයි පමණක් පරීක්ෂා කිරීම (Common pages සඳහා)
 */
function isLoggedIn() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../index.php");
        exit();
    }
}
?>