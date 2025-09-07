<?php
require_once '../includes/helpers.php'; // Include your helper functions

$action = $_GET['action'] ?? 'index';

if ($action === 'set_target') {
    redirect('../views/performance/set_target.php');
    exit;
}

if ($action === 'index') {
    redirect('../views/performance/index.php');
}
