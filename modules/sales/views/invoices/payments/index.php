<?php
require_once '../../../../../config/db.php'; // Your database connection
require_once '../../../includes/helpers.php'; // Include your helper functions

$action = $_GET['action'] ?? 'index';

if ($action === 'add') {
    redirect('add.php');
} else {
    die("Invalid Route");
    exit;
}
