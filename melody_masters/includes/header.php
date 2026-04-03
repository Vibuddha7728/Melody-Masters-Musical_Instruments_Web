<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (file_exists('includes/db_config.php')) {
    include 'includes/db_config.php'; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melody Masters</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Outfit:wght@400;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        /* --- 1. Global Theme Variables --- */
        :root {
            --main-bg: #0b0b0b;
            --main-text: #ffffff;
            --nav-bg-glass: rgba(0, 0, 0, 0.3);
            --modal-glass: rgba(20, 20, 20, 0.85);
            --input-glass: rgba(255, 255, 255, 0.07);
            --border-line: rgba(255, 193, 7, 0.1);
            --accent-gold: #ffc107;
            --card-bg: rgba(255, 255, 255, 0.05);
        }

        body.light-mode {
            --main-bg: #ffffff;
            --main-text: #121212;
            --nav-bg-glass: rgba(255, 255, 255, 0.95);
            --modal-glass: rgba(245, 245, 245, 0.98);
            --input-glass: rgba(0, 0, 0, 0.05);
            --border-line: rgba(0, 0, 0, 0.1);
            --card-bg: #f8f9fa;
        }

        /* --- 2. Base Styles --- */
        html, body {
            background-color: var(--main-bg) !important;
            color: var(--main-text) !important;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            transition: background-color 0.4s ease, color 0.4s ease;
            overflow-x: hidden;
        }

        body,
        .navbar,
        .navbar-collapse,
        .modal-content,
        .glass-input,
        .nav-link,
        .navbar-brand,
        .form-label,
        .text-warning,
        .btn,
        .slider,
        .card,
        .dropdown-menu,
        input,
        textarea,
        select {
            transition: all 0.4s ease;
        }

        h1, h2, h3, h4, h5, h6,
        p, span, label, small, li {
            color: inherit;
        }

        /* --- 3. Global Light Mode Fixes --- */
        body.light-mode,
        body.light-mode main,
        body.light-mode section,
        body.light-mode header,
        body.light-mode footer,
        body.light-mode aside {
            background-color: var(--main-bg) !important;
            color: var(--main-text) !important;
        }

        body.light-mode .container,
        body.light-mode .container-fluid,
        body.light-mode .row,
        body.light-mode [class*="col-"] {
            color: var(--main-text) !important;
        }

        body.light-mode .bg-dark,
        body.light-mode .bg-black,
        body.light-mode .text-white,
        body.light-mode .table-dark,
        body.light-mode .dropdown-menu-dark {
            background-color: var(--card-bg) !important;
            color: var(--main-text) !important;
            border-color: var(--border-line) !important;
        }

        /* --- 4. Navbar Styles --- */
        .navbar {
            padding: 15px 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar-transparent {
            background: var(--nav-bg-glass) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-line);
        }

        .navbar-scrolled {
            background: var(--nav-bg-glass) !important;
            backdrop-filter: blur(15px);
            padding: 10px 0;
            border-bottom: 1px solid var(--border-line);
        }

        .nav-link {
            color: var(--main-text) !important;
            margin: 0 10px;
            font-weight: 500;
            font-family: 'Outfit', sans-serif;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--accent-gold) !important;
        }

        .navbar-brand {
            font-family: 'Outfit', sans-serif;
            letter-spacing: 1px;
            color: var(--accent-gold) !important;
        }

        body.light-mode .navbar,
        body.light-mode nav,
        body.light-mode .navbar-collapse {
            background: var(--nav-bg-glass) !important;
            color: var(--main-text) !important;
            border-color: var(--border-line) !important;
        }

        body.light-mode .navbar-dark .navbar-nav .nav-link,
        body.light-mode .navbar-nav .nav-link,
        body.light-mode .nav-link {
            color: #333333 !important;
        }

        body.light-mode .navbar-dark .navbar-nav .nav-link:hover,
        body.light-mode .navbar-dark .navbar-nav .nav-link.active,
        body.light-mode .nav-link:hover,
        body.light-mode .nav-link.active {
            color: var(--accent-gold) !important;
        }

        body.light-mode .navbar-brand {
            color: var(--accent-gold) !important;
        }

        body.light-mode .navbar-toggler {
            border-color: rgba(0, 0, 0, 0.2) !important;
        }

        body.light-mode .navbar-toggler-icon {
            filter: invert(1);
        }

        /* --- 5. Theme Switcher Button --- */
        .theme-switch-wrapper {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 45px;
            height: 22px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #333;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: var(--accent-gold);
        }

        input:checked + .slider:before {
            transform: translateX(23px);
        }

        body.light-mode .slider {
            background-color: #cccccc !important;
        }

        body.light-mode .slider:before {
            background-color: #ffffff !important;
        }

        body.light-mode input:checked + .slider {
            background-color: var(--accent-gold) !important;
        }

        /* --- 6. Modals & Inputs --- */
        .modal-content.glass-modal {
            background: var(--modal-glass) !important;
            backdrop-filter: blur(25px) saturate(180%) !important;
            border: 1px solid var(--border-line) !important;
            border-radius: 25px;
            color: var(--main-text) !important;
        }

        .glass-input {
            background: var(--input-glass) !important;
            border: 1px solid var(--border-line) !important;
            color: var(--main-text) !important;
            border-radius: 12px !important;
            padding: 12px 15px !important;
            box-shadow: none !important;
        }

        .glass-input::placeholder {
            color: var(--main-text) !important;
            opacity: 0.65;
        }

        .glass-input:focus {
            background: var(--input-glass) !important;
            color: var(--main-text) !important;
            border: 1px solid var(--accent-gold) !important;
            box-shadow: 0 0 0 0.15rem rgba(255, 193, 7, 0.15) !important;
        }

        body.light-mode .modal-content,
        body.light-mode .glass-modal,
        body.light-mode .dropdown-menu,
        body.light-mode .card,
        body.light-mode .shop-card,
        body.light-mode .product-card {
            background: var(--modal-glass) !important;
            color: var(--main-text) !important;
            border: 1px solid var(--border-line) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
        }

        body.light-mode input,
        body.light-mode textarea,
        body.light-mode select,
        body.light-mode .form-control,
        body.light-mode .form-select,
        body.light-mode .glass-input {
            background: var(--input-glass) !important;
            color: #000000 !important;
            border: 1px solid var(--border-line) !important;
            box-shadow: none !important;
        }

        body.light-mode input::placeholder,
        body.light-mode textarea::placeholder {
            color: #6c757d !important;
            opacity: 1 !important;
        }

        body.light-mode input:focus,
        body.light-mode textarea:focus,
        body.light-mode select:focus,
        body.light-mode .form-control:focus,
        body.light-mode .form-select:focus,
        body.light-mode .glass-input:focus {
            background: var(--input-glass) !important;
            color: #000000 !important;
            border-color: var(--accent-gold) !important;
            box-shadow: 0 0 0 0.15rem rgba(255, 193, 7, 0.2) !important;
        }

        .btn-theme-gradient { 
            background: linear-gradient(45deg, #ffc107, #ff8f00); 
            border: none;
            color: #000;
            font-weight: 700;
            border-radius: 12px;
            padding: 12px;
            text-transform: uppercase;
        }

        body.light-mode .btn-close,
        body.light-mode .btn-close-white {
            filter: invert(1) grayscale(100%);
        }

        body.light-mode .text-warning {
            color: #d39e00 !important;
        }

        body.light-mode .text-muted {
            color: #6c757d !important;
        }

        body.light-mode .btn-outline-warning {
            color: #b8860b !important;
            border-color: #b8860b !important;
        }

        body.light-mode .btn-outline-warning:hover {
            background-color: #b8860b !important;
            color: #fff !important;
        }

        body.light-mode .btn-outline-danger {
            color: #dc3545 !important;
            border-color: #dc3545 !important;
        }

        body.light-mode .btn-warning,
        body.light-mode .btn-theme-gradient {
            color: #000 !important;
        }

        body.light-mode a:not(.btn):not(.navbar-brand):not(.nav-link) {
            color: #0056b3 !important;
        }

        body.light-mode a:not(.btn):not(.navbar-brand):not(.nav-link):hover {
            color: #003d80 !important;
        }

        body.light-mode .dropdown-item {
            color: var(--main-text) !important;
            background-color: transparent !important;
        }

        body.light-mode .dropdown-item:hover,
        body.light-mode .dropdown-item:focus {
            background-color: #e9ecef !important;
            color: #000000 !important;
        }

        /* =============================================
        MOBILE & TABLET RESPONSIVENESS
        ============================================= */

        @media (max-width: 991px) {
            .navbar-collapse {
                background: var(--modal-glass) !important;
                backdrop-filter: blur(20px);
                margin-top: 15px;
                padding: 20px;
                border-radius: 20px;
                border: 1px solid var(--border-line);
            }

            .nav-link {
                margin: 10px 0;
                text-align: center;
            }

            .theme-switch-wrapper {
                margin: 15px 0;
                justify-content: center;
            }

            .navbar-nav + .d-flex {
                flex-direction: column;
                width: 100%;
                gap: 10px;
            }

            .navbar-nav + .d-flex button, 
            .navbar-nav + .d-flex a {
                width: 100%;
            }

            .navbar-nav + .d-flex .text-warning {
                margin-bottom: 10px;
                text-align: center;
                display: block;
            }

            body.light-mode .navbar-collapse {
                background: var(--modal-glass) !important;
            }
        }

        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.4rem !important;
            }

            .modal-dialog {
                margin: 15px;
            }

            .glass-modal {
                border-radius: 20px;
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top navbar-transparent" id="mainNav">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3" href="index.php">MELODY MASTERS</a>
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
                <li class="nav-item"><a class="nav-link" href="customer/my_account.php">My Account</a></li>
                <li class="nav-item"><a class="nav-link" href="cart.php">Cart <i class="bi bi-cart3"></i></a></li>
                <li class="nav-item"><a class="nav-link" href="about_us.php">About Us</a></li>
            </ul>

            <div class="d-flex align-items-center">
                <div class="theme-switch-wrapper">
                    <i id="mode-icon" class="bi bi-moon-stars-fill text-warning me-2"></i>
                    <label class="switch">
                        <input type="checkbox" id="theme-toggle">
                        <span class="slider"></span>
                    </label>
                </div>

                <?php if(!isset($_SESSION['user_id'])): ?>
                    <button class="btn btn-outline-warning btn-sm me-lg-2 rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
                    <button class="btn btn-warning btn-sm rounded-pill px-4 fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#registerModal">Register</button>
                <?php else: ?>
                    <span class="text-warning fw-medium me-lg-3">Hi, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal">
            <div class="modal-header border-0">
                <h4 class="fw-bold text-warning m-0">Welcome Back</h4>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="auth.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label small text-warning fw-bold">Email Address</label>
                        <input type="email" name="email" class="form-control glass-input" required placeholder="Enter your email">
                    </div>
                    <div class="mb-4">
                        <label class="form-label small text-warning fw-bold">Password</label>
                        <input type="password" name="password" class="form-control glass-input" required placeholder="Enter password">
                    </div>
                    <button type="submit" name="login" class="btn btn-theme-gradient w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-modal">
            <div class="modal-header border-0">
                <h4 class="fw-bold text-warning m-0">Create Account</h4>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="auth.php" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small text-warning fw-bold">Username</label>
                                <input type="text" name="username" class="form-control glass-input" required placeholder="Choose a username">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-warning fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control glass-input" required placeholder="Enter your email">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-warning fw-bold">Password</label>
                                <input type="password" name="password" class="form-control glass-input" required placeholder="Create a password">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small text-warning fw-bold">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control glass-input" required placeholder="Enter phone number">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-warning fw-bold">Delivery Address</label>
                                <textarea name="address" class="form-control glass-input" rows="5" required placeholder="Enter delivery address"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-2">
                        <button type="submit" name="register" class="btn btn-theme-gradient w-100">Register Now</button>
                        <p class="text-center mt-3 small opacity-75">Already have an account? <a href="#" class="text-warning" data-bs-target="#loginModal" data-bs-toggle="modal">Login here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // 1. Navbar Scroll Logic
    window.addEventListener('scroll', function () {
        const nav = document.getElementById('mainNav');
        if (window.scrollY > 50) {
            nav.classList.add('navbar-scrolled');
        } else {
            nav.classList.remove('navbar-scrolled');
        }
    });

    // 2. Global Theme Switch Logic
    const themeToggle = document.getElementById('theme-toggle');
    const modeIcon = document.getElementById('mode-icon');
    const body = document.body;

    function applyTheme(theme) {
        if (theme === 'light') {
            body.classList.add('light-mode');
            if (themeToggle) themeToggle.checked = true;
            if (modeIcon) {
                modeIcon.classList.remove('bi-moon-stars-fill');
                modeIcon.classList.add('bi-sun-fill');
            }
        } else {
            body.classList.remove('light-mode');
            if (themeToggle) themeToggle.checked = false;
            if (modeIcon) {
                modeIcon.classList.remove('bi-sun-fill');
                modeIcon.classList.add('bi-moon-stars-fill');
            }
        }
    }

    const savedTheme = localStorage.getItem('theme') || 'dark';
    applyTheme(savedTheme);

    if (themeToggle) {
        themeToggle.addEventListener('change', () => {
            const newTheme = themeToggle.checked ? 'light' : 'dark';
            applyTheme(newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }
</script>

</body>
</html>