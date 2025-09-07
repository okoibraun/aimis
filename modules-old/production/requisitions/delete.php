<?php
require_once '../../../config/db.php';
$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM production_requisition_items WHERE requisition_id = $id");
mysqli_query($conn, "DELETE FROM production_requisitions WHERE id = $id");
header("Location: index.php");
exit;