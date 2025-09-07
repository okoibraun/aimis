<?php
session_start();
include('../config/db.php');
include_once('../includes/audit_log.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$id = intval($_GET['id']);

if ($id > 0) {
    mysqli_query($conn, "DELETE FROM users WHERE id = $id");
}

//Log Audit
log_audit($conn, $_SESSION['user_id'], 'Delete User', 'Delete User for user ID: '.$id);

header('Location: index.php');
exit();
?>