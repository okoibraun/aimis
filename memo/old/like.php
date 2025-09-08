<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$memo_id = intval($_POST['memo_id']);
$user_id = $_SESSION['user_id'];

if (isset($_POST['like'])) {
    mysqli_query($conn, "INSERT IGNORE INTO memo_likes (memo_id, user_id) VALUES ($memo_id, $user_id)");
    include_once('../includes/audit_log.php');
    log_audit($conn, $user_id, 'Memo Like', "Liked memo ID $memo_id");
} else if (isset($_POST['unlike'])) {
    mysqli_query($conn, "DELETE FROM memo_likes WHERE memo_id=$memo_id AND user_id=$user_id");
    include_once('../includes/audit_log.php');
    log_audit($conn, $user_id, 'Memo Unlike', "Unliked memo ID $memo_id");
}

header("Location: memo.php?id=$memo_id");
exit();
?>
