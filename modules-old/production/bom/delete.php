<?php
require_once '../../../config/db.php';
$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM production_bom_items WHERE bom_id = $id");
mysqli_query($conn, "DELETE FROM production_bom WHERE id = $id");
header("Location: index.php");
exit;