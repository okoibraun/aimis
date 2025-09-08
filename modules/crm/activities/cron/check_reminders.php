<?php
require_once '../../../../config/db.php';
$now = date('Y-m-d H:i:s');

$stmt = $conn->prepare("
    SELECT id, subject, assigned_to, reminder_at
    FROM crm_activities
    WHERE reminder_at IS NOT NULL
      AND is_reminder_sent = 0
      AND reminder_at <= ?
");
$stmt->bind_param("s", $now);
$stmt->execute();
$results = $stmt->get_result();

while ($row = $results->fetch_assoc()) {
    // 1. Notify user (example: log alert or email — extend here)
    $user_id = $row['assigned_to'];
    $subject = $row['subject'];

    // TODO: Replace with actual alert/email dispatch
    file_put_contents('../../logs/reminder_log.txt', "[{$now}] Reminder for Activity #{$row['id']}: $subject (User $user_id)\n", FILE_APPEND);

    // 2. Mark as sent
    $mark = $conn->prepare("UPDATE crm_activities SET is_reminder_sent = 1 WHERE id = ?");
    $mark->bind_param("i", $row['id']);
    $mark->execute();
}
