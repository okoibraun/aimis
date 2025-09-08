<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$memo_id = intval($_POST['memo_id']);
$user_id = $_SESSION['user_id'];
$comment = mysqli_real_escape_string($conn, $_POST['comment']);

mysqli_query($conn, "INSERT INTO memo_comments (memo_id, user_id, comment) VALUES ($memo_id, $user_id, '$comment')");

// Optional: Audit Log
include_once('../includes/audit_log.php');
log_audit($conn, $user_id, 'Memo Comment', "Commented on memo ID $memo_id");

header("Location: memo.php?id=$memo_id");
exit();
?>