<?php
function notify_user($conn, $user_id, $message) {
    $user_id = intval($user_id);
    $message = mysqli_real_escape_string($conn, $message);

    $sql = "INSERT INTO notifications (user_id, message) VALUES ($user_id, '$message')";
    mysqli_query($conn, $sql);
}
?>