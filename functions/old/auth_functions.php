<?php
require_once __DIR__ . '/../config/db.php';

function login_user($email, $password) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1 LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password_hash'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            return true;
        }
    }
    return false;
}

function is_logged_in() {
    session_start();
    return isset($_SESSION['user_id']);
}

function logout_user() {
    session_start();
    session_destroy();
    header("Location: ../auth/login.php");
    exit();
}


function get_user($user_id) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function get_user_by_email($email) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
