<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/helpers.php';

/**
 * Create a new user account.
 */
// function create_user($email, $password, $full_name, $is_active = 1) {
//     global $mysqli;

//     $password_hash = password_hash($password, PASSWORD_BCRYPT);

//     $stmt = $mysqli->prepare("
//         INSERT INTO users (email, password_hash, full_name, is_active) 
//         VALUES (?, ?, ?, ?)
//     ");
//     $stmt->bind_param('sssi', $email, $password_hash, $full_name, $is_active);
//     return $stmt->execute();
// }

function create_user($name, $email, $role, $company_id, $password) {
    global $conn;

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $company_name = "";

    $stmt = $conn->prepare("INSERT INTO users (name, email, role, company_id, company_name, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiss", $name, $email, $role, $company_id, $company_name, $hashed_password);
    return $stmt->execute();
}

function get_user_by_id($user_id) {
    global $conn;

    $query = "SELECT * FROM users WHERE id=$user_id";
    $execute = mysqli_query($conn, $query);
    $result = mysqli_fetch_assoc($execute);

    return $result;
}

/**
 * Update user
 */
function update_user($user_id, $full_name, $role, $company_id, $employee_id, $status) {
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET name = ?, role = ?, company_id = ?, employee_id = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssiisi", $full_name, $role, $company_id, $employee_id, $status, $user_id);
    return $stmt->execute();
}

/**
 * Update user profile
 */
function update_user_profile($user_id, $name, $email, $password = null) {
    global $conn;
    $query = "UPDATE users SET name=?, email=? WHERE id=?";
    $params = [$name, $email, $user_id];

    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET name=?, email=?, password=? WHERE id=?";
        $params = [$name, $email, $hashed, $user_id];
    }

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
}


/**
 * Delete user
 */
function delete_user($user_id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    return $stmt->execute();
}

/**
 * Assign a user to a company.
 */
function assign_user_to_company($user_id, $company_id, $role_id) {
    global $mysqli;

    $stmt = $mysqli->prepare("
        INSERT INTO user_company (user_id, company_id, role_id) 
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param('iii', $user_id, $company_id, $role_id);
    return $stmt->execute();
}

/**
 * Get all users in a company.
 */
// function get_users_by_company($company_id) {
//     global $mysqli;

//     $stmt = $mysqli->prepare("
//         SELECT u.id, u.email, u.name, uc.role_id, r.name AS role_name
//         FROM users u
//         JOIN user_company uc ON u.id = uc.user_id
//         LEFT JOIN roles r ON uc.role_id = r.id
//         WHERE uc.company_id = ?
//     ");
//     $stmt->bind_param('i', $company_id);
//     $stmt->execute();
//     return $stmt->get_result();
// }

function get_users_by_company($company_id = null) {
    global $conn;

    $query = "
        SELECT u.*, c.name AS company_name
        FROM users u
        LEFT JOIN companies c ON u.company_id = c.id
    ";

    if ($company_id) {
        $stmt = $conn->prepare($query . " WHERE u.company_id = ?");
        $stmt->bind_param("i", $company_id);
    } else {
        $stmt = $conn->prepare($query);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}


/**
 * Check if a user exists by email.
 */
function user_exists($email) {
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

/**
 * Get a user by email.
 */
function get_user_by_email($email) {
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Create a new user from invitation.
 */
function create_user_from_invitation($invitation, $full_name, $hashed_password) {
    global $conn;

    // Avoid duplication
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $invitation['email']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        return false;
    }

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, company_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $full_name, $invitation['email'], $hashed_password, $invitation['role'], $invitation['company_id']);
    return $stmt->execute();
}

function send_confirmation_email($email, $token) {
    $subject = "Confirm Your Email!";
    $link = "https://aimiscloud.com.ng/modules/user/confirm-email.php?a=ce&token=" . urlencode($token);

    $message = "
    <html>
    <head>
      <title>Confirm Your Email</title>
    </head>
    <body style='font-family: Arial, sans-serif;'>
      <h2 style='color: #333;'>Activate your account by confirming your email address</h2>
      <p>Hello {$email},</p>
      <p>You need to confirm your email address to activate your account. Click the button below to confirm your invitation:</p>
      <p style='text-align: center; margin: 20px;'>
        <a href='$link' style='padding: 10px 20px; background-color: #007BFF; color: #fff; text-decoration: none; border-radius: 5px;'>Confirm Email</a>
      </p>
      <p>If you did not request this, you can safely ignore this email.</p>
    </body>
    </html>";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: AIMIS Cloud <no-reply@aimiscloud.com.ng>";

    return mail($email, $subject, $message, $headers);
}

function send_reset_password_email($email, $token) {
    $subject = "Reset Password Email!";
    $link = "https://aimiscloud.com.ng/reset_password.php?a=rp&token=" . urlencode($token);

    $message = "
    <html>
    <head>
      <title>Reset Password Email</title>
    </head>
    <body style='font-family: Arial, sans-serif;'>
      <h2 style='color: #333;'>Password Reset Request for Email: {$email}</h2>
      <p>Hello {$email},</p>
      <p>If you've lost your password or wish to reset it, use the link below to get started:</p>
      <p style='text-align: center; margin: 20px;'>
        <a href='$link' style='padding: 10px 20px; background-color: #007BFF; color: #fff; text-decoration: none; border-radius: 5px;'>Reset your password</a>
      </p>
      <p>If you did not request this, you can safely ignore this email.</p>
    </body>
    </html>";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: AIMIS Cloud <no-reply@aimiscloud.com.ng>";

    return mail($email, $subject, $message, $headers);
}