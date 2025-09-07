<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/helpers.php';

/**
 * Create an invitation record for a user.
 */
// function create_invitation($email, $company_id, $invited_by, $role_id, $expires_in_hours = 24) {
//     global $mysqli;

//     $token = bin2hex(random_bytes(32));
//     $expires_at = date('Y-m-d H:i:s', strtotime("+$expires_in_hours hours"));

//     $stmt = $mysqli->prepare("
//         INSERT INTO invitations (email, token, company_id, invited_by, role_id, expires_at)
//         VALUES (?, ?, ?, ?, ?, ?)
//     ");
//     $stmt->bind_param('ssiiss', $email, $token, $company_id, $invited_by, $role_id, $expires_at);

//     if ($stmt->execute()) {
//         // Stub: send email with token
//         // You can hook this to PHPMailer or similar in production
//         $link = BASE_URL . "modules/user/confirm.php?token=$token";
//         file_put_contents(__DIR__ . '/../logs/email_debug.log', "Invite sent to $email: $link\n", FILE_APPEND);
//         return true;
//     }

//     return false;
// }

function create_invitation($email, $role, $company_id, $token) {
    global $conn;

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        return false; // User already exists
    }

    $stmt = $conn->prepare("INSERT INTO invitations (email, role, company_id, token) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $email, $role, $company_id, $token);
    return $stmt->execute();
}

/**
 * Send invitation email.
 */
// function send_invitation_email($email, $token) {
//     $subject = "You're invited to join AIMIS";
//     $link = "https://yourdomain.com/modules/user/confirm.php?token=$token";
//     $message = "Hello,\n\nYou've been invited to join AIMIS. Please complete your registration:\n$link\n\nThanks.";
//     // Use PHP mail() function or external service (e.g., PHPMailer in future)
//     mail($email, $subject, $message);
// }

function send_invitation_email($email, $token) {
    $subject = "You're Invited to AIMIS!";
    $link = "https://aimiscloud.com.ng/modules/user/confirm.php?token=" . urlencode($token);

    $message = "
    <html>
    <head>
      <title>You're Invited to AIMIS</title>
    </head>
    <body style='font-family: Arial, sans-serif;'>
      <h2 style='color: #333;'>You've been invited to join AIMIS</h2>
      <p>Hello,</p>
      <p>You’ve been invited to join the AIMIS platform. Click the button below to confirm your invitation:</p>
      <p style='text-align: center; margin: 20px;'>
        <a href='$link' style='padding: 10px 20px; background-color: #007BFF; color: #fff; text-decoration: none; border-radius: 5px;'>Accept Invitation</a>
      </p>
      <p>If you did not request this, you can safely ignore this email.</p>
    </body>
    </html>";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: AIMIS <no-reply@aimiscloud.com.ng>";

    return mail($email, $subject, $message, $headers);
}


/**
 * Get invitation by token.
 */
function get_invitation_by_token($token) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM invitations WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Delete invitation by token.
 */
function delete_invitation($token) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM invitations WHERE token = ?");
    $stmt->bind_param("s", $token);
    return $stmt->execute();
}

/**
 * Validate invitation token and fetch invitation info.
 */
function get_valid_invitation($token) {
    global $mysqli;

    $stmt = $mysqli->prepare("
        SELECT * FROM invitations 
        WHERE token = ? AND expires_at > NOW() AND accepted_at IS NULL
        LIMIT 1
    ");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Mark invitation as accepted.
 */
function mark_invitation_accepted($token) {
    global $mysqli;

    $stmt = $mysqli->prepare("
        UPDATE invitations SET accepted_at = NOW() WHERE token = ?
    ");
    $stmt->bind_param('s', $token);
    return $stmt->execute();
}
