<?php
include('../config/db.php');

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = intval($_GET['id']);

$query = "DELETE FROM memos WHERE id = $id";

if (mysqli_query($conn, $query)) {
    // Optional: Audit Log
    include_once('../includes/audit_log.php');
    log_audit($conn, $user_id, 'Delete', "Deleted memo ID $memo_id");

    header('Location: index.php?msg=MemoDeleted');
    exit();
} else {
    echo "Error deleting memo: " . mysqli_error($conn);
}
?>