<?php
require_once '../../../config/db.php';
require_once '../../../functions/sales/orders.php';

$action = $_GET['action'] ?? 'list';

switch ($action) {
  case 'list':
    include '../modules/sales/views/orders/list.php';
    break;

  case 'form':
    include '../modules/sales/views/orders/form.php';
    break;

  case 'save':
    handle_order_form_submission();
    break;

  case 'delete':
    delete_order($_GET['id']);
    break;

  default:
    include '../modules/sales/views/orders/list.php';
}
