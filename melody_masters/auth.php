<?php 
session_start();
include 'includes/db_config.php';

// --- 1. ලියාපදිංචි වීමේ කොටස (Registration Logic) ---
if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    
    // ආරක්ෂාව සඳහා සියලුම නව ලියාපදිංචි කිරීම් 'customer' ලෙස සලකනු ලැබේ.
    $role = 'customer'; 

    $checkEmail = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already exists!'); window.location='index.php';</script>";
    } else {
        $sql = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Registration Successful! Please Login.'); window.location='index.php';</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

// --- 2. ඇතුළු වීමේ කොටස (Login Logic) ---
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Database එකෙන් පරිශීලකයා සෙවීම
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Password එක පරීක්ෂා කිරීම
        if (password_verify($password, $user['password'])) {
            // Session එකේ දත්ත ගබඩා කිරීම
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            /* --- REDIRECTION LOGIC ---
               පරිශීලකයාගේ Role එක අනුව නිවැරදි Dashboard එකට යැවීම.
            */
            if ($user['role'] == 'admin') {
                header("Location: admin/admin_dashboard.php");
                exit();
            } 
            elseif ($user['role'] == 'staff') {
                header("Location: staff/staff_dashboard.php");
                exit();
            } 
            else {
                // සාමාන්‍ය Customer කෙනෙක් නම් Shop එකට හෝ Index එකට
                header("Location: shop.php"); 
                exit();
            }
        } else {
            // වැරදි මුරපදයක් නම්
            echo "<script>alert('Incorrect Password!'); window.location='index.php';</script>";
        }
    } else {
        // අදාළ Email එකෙන් පරිශීලකයෙකු නොමැති නම්
        echo "<script>alert('No user found with this email!'); window.location='index.php';</script>";
    }
}
?>