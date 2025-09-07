<?php
require_once '../../includes/helpers.php';
include("../../../../functions/role_functions.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

$action = $_GET['action'];

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
        // include '../views/quotations/form.php';
        break;

    case 'save':
        $data = $_POST;
        // post fields
        $lead_id = $data['lead_id'];
        $company_id = $data['company_id'];
        $customer_id = $data['customer_id'];
        $quote_number = $data['quote_number'];
        $quotation_date = $data['quotation_date'];
        $valid_until = $data['valid_until'];
        $status = $data['status'];
        $total = $data['total'];
        $tax_amount = $data['tax_amount'];
        $created_by = $data['created_by'];

        $id = $data['id'] ?? null;

        $fields = [
            'lead_id', 'company_id', 'customer_id', 'quote_number', 'quotation_date', 'valid_until',
            'status', 'total', 'created_by'
        ];

        //$input = array_intersect_key($data, array_flip($fields));

        if ($id) { //edit/ update
            //update_by_id('sales_quotations', $id, $input);
            $quote_stmt = $conn->prepare("UPDATE sales_quotations SET lead_id=?, company_id=?, customer_id=?, quote_number=?, quotation_date=?, valid_until=?, status=?, total=?, tax=?, created_by=? WHERE id=?");
            $quote_stmt->bind_param("iiissssddsi", $lead_id, $company_id, $customer_id, $quote_number, $quotation_date, $valid_until, $status, $total, $tax_amount, $created_by, $id);
            $quote_stmt->execute();

            // Insert quotation items
            $quotation_id = $id;
            $product_ids = $_POST['items_product_id'] ?? [];
            $item_quantities = $_POST['items_quantity'];
            $item_unit_prices = $_POST['items_unit_price'];
            $item_discounts = $_POST['items_discount_percent'];
            $totals = $_POST['items_total'];

            $delete_prev_quotation_items = $conn->query("DELETE FROM sales_quotation_items WHERE quotation_id = $id");
            if($delete_prev_quotation_items) {
                foreach ($product_ids as $index => $product_id) {
                    $product_id = $product_ids[$index];
                    $quantity = floatval($item_quantities[$index]);
                    $unit_price = floatval($item_unit_prices[$index]);
                    $discount = floatval($item_discounts[$index]);
                    $total = floatval($totals[$index]);
    
                    if (!empty($product_id)) {
                        $stmt_item = $conn->prepare("INSERT INTO sales_quotation_items (quotation_id, product_id, quantity, unit_price, discount, total)
                            VALUES (?, ?, ?, ?, ?, ?)
                        ");
                        $sub_total = floatval($item_sub_totals[$index]);
                        $stmt_item->bind_param('iiiddd', $quotation_id, $product_id, $quantity, $unit_price, $discount, $total);
                        $stmt_item->execute();
                    }
                }
            }
        } else {
            // insert_row('sales_quotations', $input);
            $quote_stmt = $conn->prepare("
                INSERT INTO sales_quotations (lead_id, company_id, customer_id, quote_number, quotation_date, valid_until, status, total, tax, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $quote_stmt->bind_param("iiissssdds", $lead_id, $company_id, $customer_id, $quote_number, $quotation_date, $valid_until, $status, $total, $tax_amount, $created_by);
            $quote_stmt->execute();
            $id = $conn->insert_id; // Get the last inserted ID

            // Insert quotation items
            $quotation_id = $id;
            $product_ids = $_POST['items_product_id'] ?? [];
            $item_quantities = $_POST['items_quantity'];
            $item_unit_prices = $_POST['items_unit_price'];
            $item_discounts = $_POST['items_discount_percent'];
            $totals = $_POST['items_total'];

            foreach ($product_ids as $index => $product_id) {
                $product_id = $product_ids[$index];
                $quantity = floatval($item_quantities[$index]);
                $unit_price = floatval($item_unit_prices[$index]);
                $discount = floatval($item_discounts[$index]);
                $total = floatval($totals[$index]);

                if (!empty($product_id)) {
                    $stmt_item = $conn->prepare("INSERT INTO sales_quotation_items (quotation_id, product_id, quantity, unit_price, discount, total)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    $sub_total = floatval($item_sub_totals[$index]);
                    $stmt_item->bind_param('iiiddd', $quotation_id, $product_id, $quantity, $unit_price, $discount, $total);
                    $stmt_item->execute();
                }
            }
        }

        header('Location: ./');
        exit;

    case 'delete':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $conn->query("DELETE FROM sales_quotation_items WHERE quotation_id = $id");
            $conn->query("DELETE FROM sales_quotations WHERE id = $id AND company_id = $company_id");
        }
        // header('Location: quotations.php');
        header('Location: ./');
        exit;

    default:
        echo "Invalid action.";
        exit;
}
