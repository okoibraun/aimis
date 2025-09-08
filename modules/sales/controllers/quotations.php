<?php
require_once '../../../config/db.php';
require_once '../includes/helpers.php';

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
        $quotations = get_all_rows('sales_quotations', 'quote_date DESC');
        include '../views/quotations/';
        break;

    case 'form':
        $id = $_GET['id'] ?? null;
        $quotation = $id ? get_row_by_id('sales_quotations', $id) : null;
        $products = get_all_rows('sales_products', 'name ASC');
        $customers = get_all_rows('sales_customers', 'company_name ASC');
        include '../views/quotations/form';
        break;

    case 'save':
        $data = $_POST;
        $id = $_POST['id'] ?? null;

        $fields = [
            'lead_id', 'customer_id', 'quote_number', 'quote_date', 'expiry_date',
            'status', 'total_amount', 'tax_amount', 'created_by'
        ];

        $input = array_intersect_key($data, array_flip($fields));

        if ($id) {
            update_by_id('sales_quotations', $id, $input);
        } else {
            insert_row('sales_quotations', $input);
        }

        header('Location: quotations.php');
        exit;

    case 'delete':
        $id = $_GET['id'] ?? null;
        if ($id) {
            delete_by_id('sales_quotations', $id);
        }
        header('Location: quotations.php');
        exit;

    default:
        echo "Invalid action.";
        exit;
}
