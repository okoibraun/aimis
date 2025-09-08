<?php
include '../../config/db.php';
$id = $_GET['id'];
$status = $_GET['status'];

$conn->query("UPDATE leave_requests SET status='$status' WHERE id=$id");

header("Location: view_requests.php");
exit;
