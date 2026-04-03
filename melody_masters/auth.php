<?php 
session_start();
include 'includes/db_config.php';

// --- 1. ලියාපදිංචි වීමේ කොටස (Registration Logic) ---
if (isset($_POST['register'])) {
    // පෝරමයෙන් ලැබෙන දත්ත ආරක්ෂිතව ලබා ගැනීම
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // නව ක්ෂේත්‍ර (Phone Number සහ Address) ලබා ගැනීම
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']); 
    $address = mysqli_real_escape_string($conn, $_POST['address']);           
    
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    
    // පද්ධතියේ භූමිකාව (Role) තීරණය කිරීම
    $role = 'customer'; 

    // ඊමේල් එක දැනටමත් පද්ධතියේ තිබේදැයි පරීක්ෂා කිරීම
    $checkEmail = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already exists!'); window.location='index.php';</script>";
    } else {
        // නව දත්ත දත්ත සමුදායට (Database) ඇතුළත් කිරීම
        $sql = "INSERT INTO users (username, email, phone_number, address, password, role) 
                VALUES ('$username', '$email', '$phone_number', '$address', '$password', '$role')";
        
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

            /* --- REDIRECTION LOGIC --- */
            if ($user['role'] == 'admin') {
                header("Location: admin/admin_dashboard.php");
                exit();
            } 
            elseif ($user['role'] == 'staff') {
                header("Location: staff/staff_dashboard.php");
                exit();
            } 
            else {
                header("Location: shop.php"); 
                exit();
            }
        } else {
            echo "<script>alert('Incorrect Password!'); window.location='index.php';</script>";
        }
    } else {
        echo "<script>alert('No user found with this email!'); window.location='index.php';</script>";
    }
}
?>