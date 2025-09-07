<?php
require_once __DIR__ . '/../config/db.php';

function send_invitation($email, $company_id, $role_id, $invited_by) {
    global $conn;
    
    // Generate a unique token
    $token = bin2hex(random_bytes(32));

    $stmt = $conn->prepare("INSERT INTO invitations (email, company_id, role_id, token, invited_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("siisi", $email, $company_id, $role_id, $token, $invited_by);
    if ($stmt->execute()) {
        // Simulate email send (in production, use PHPMailer or SMTP)
        $link = "http://localhost:8081/aimis/modules/user/invite_accept.php?token=" . $token;
        file_put_contents(__DIR__ . '/../storage/invitations.log', "Invite sent to $email: $link\n", FILE_APPEND);
        return true;
    }
    return false;
}

function get_invite_by_token($token) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM invitations WHERE token = ? AND status = 'pending' LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function mark_invite_accepted($invite_id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE invitations SET status = 'accepted' WHERE id = ?");
    $stmt->bind_param("i", $invite_id);
    return $stmt->execute();
}

function mark_invite_declined($invite_id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE invitations SET status = 'declined' WHERE id = ?");
    $stmt->bind_param("i", $invite_id);
    return $stmt->execute();
}