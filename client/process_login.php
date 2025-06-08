<?php
session_start();
require_once '../config/Database.php';

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'login_errors.log');

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        
        // Log input data
        error_log('=== New Login Attempt ===');
        error_log('Email input: ' . $email);
        
        // Truy vấn user từ database
        $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        error_log('User found: ' . ($user ? 'Yes' : 'No'));
        if ($user) {
            error_log('Password verification: ' . (password_verify($password, $user['password']) ? 'Success' : 'Failed'));
        }
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            // Chuyển hướng dựa vào role của user
            if ($user['role'] === 'admin') {
                header('Location: ../public/dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $_SESSION['login_error'] = 'Email hoặc mật khẩu không đúng';
            header('Location: login.php');
            exit;
        }
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    $_SESSION['login_error'] = 'Có lỗi xảy ra, vui lòng thử lại sau';
    header('Location: login.php');
    exit;
}

