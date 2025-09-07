<?php
require_once '../../../config/db.php';
$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM production_work_orders WHERE id = $id");
header("Location: index.php");
exit;