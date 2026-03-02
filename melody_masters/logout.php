<?php
/**
 * Melody Masters - Logout Script
 */

// 1. Session එක ආරම්භ කරන්න (Destroy කරන්න කලින් start කරන්නම ඕනේ)
session_start();

// 2. සියලුම Session variables ඉවත් කරන්න
$_SESSION = array();

// 3. Browser එකේ තියෙන Session Cookie එකත් ඉවත් කරන්න (වැඩි ආරක්ෂාව සඳහා)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Session එක සම්පූර්ණයෙන්ම විනාශ (Destroy) කරන්න
session_destroy();

// 5. පරිශීලකයාව නැවත index.php පිටුවට යොමු කරන්න
header("Location: index.php?status=logged_out");
exit();
?>