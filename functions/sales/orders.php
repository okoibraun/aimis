<?php
include("../../../../role_functions.php");

function handle_order_form_submission() {
    global $db;
    global $company_id;
    global $user_id;
    global $employee_id;

    $id = $_POST['id'] ?? null;
    $customer_id = $_POST['customer_id'];
    $order_date = $_POST['order_date'];
    $delivery_date = $_POST['delivery_date'] ?? null;
    $status = $_POST['status'];
    $notes = $_POST['notes'] ?? '';

    $tax_amount = $_POST['tax_amount'] ?? 0;
    $total_amount = $_POST['total_amount'] ?? 0;

    if ($id) {
        $db->query("UPDATE sales_orders SET company-id=?, user_id=?, employee_id=?, customer_id=?, order_date=?, delivery_date=?, status=?, notes=?, tax_amount=?, total_amount=? WHERE id=?",
            [$company_id, $user_id, $employee_id, $customer_id, $order_date, $delivery_date, $status, $notes, $tax_amount, $total_amount, $id]);
    } else {
        $order_number = generate_unique_order_number();
        $db->query("INSERT INTO sales_orders (company_id, user_id, employee_id, order_number, customer_id, order_date, delivery_date, status, notes, tax_amount, total_amount) 
                    VALUES ($company_id, $user_id, $employee_id, '$order_number', '$customer_id', '$order_date', '$delivery_date', '$status', '$notes', '$tax_amount', '$total_amount')");

        //$id = $db->insert_id();
    }

    header("Location: /modules/sales/views/orders?action=list");
    exit;
}

function delete_order($id) {
    global $db;
    $db->query("DELETE FROM sales_order_items WHERE order_id = $id");
    $db->query("DELETE FROM sales_orders WHERE id = $id");

    header("Location: /modules/sales/views/orders?action=list");
    exit;
}

function generate_unique_order_number() {
    return 'ORD-' . strtoupper(uniqid());
}
