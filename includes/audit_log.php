<?php
include("../functions/role_functions.php");
function log_audit($conn, $user_id, $action, $description) {
    global $company_id;
    $user_id = intval($user_id);
    $action = mysqli_real_escape_string($conn, $action);
    $description = mysqli_real_escape_string($conn, $description);

    $sql = "INSERT INTO audit_logs (company_id, user_id, action, description) 
            VALUES ($company_id, $user_id, '$action', '$description')";
    mysqli_query($conn, $sql);
}
?>