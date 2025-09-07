<?php
require_once '../includes/helpers.php';
include("../../../functions/role_functions.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                add_product($_POST);
                break;
            case 'edit':
                update_product($_POST);
                break;
            case 'delete':
                delete_product($_POST['id']);
                redirect("../views/products/");
                break;
        }
    }
}

function add_product($data) {
    global $conn;
    global $company_id;
    global $user_id;
    global $employee_id;

    $stmt = $conn->prepare("INSERT INTO sales_products (company_id, user_id, employee_id, name, description, price, discount_type, discount_value, price_includes_tax, tax_rate, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiissdsdidi",
        $company_id,
        $user_id,
        $employee_id,
        $data['name'],
        $data['description'],
        $data['price'],
        $data['discount_type'],
        $data['discount_value'],
        $data['price_includes_tax'],
        $data['tax_rate'],
        $data['is_active']
    );

    if($stmt->execute()) {
        redirect("../views/products/");
    };
}

function update_product($data) {
    global $conn;
    global $company_id;
    global $user_id;
    global $employee_id;

    $stmt = $conn->prepare("UPDATE sales_products SET company_id = ?, user_id = ?, employee_id = ?, name = ?, description = ?, price = ?, discount_type = ?, discount_value = ?, price_includes_tax = ?, tax_rate=?, is_active = ? WHERE id = ?");
    $stmt->bind_param("iiissdsdidii",
        $company_id,
        $user_id,
        $employee_id,
        $data['name'],
        $data['description'],
        $data['price'],
        $data['discount_type'],
        $data['discount_value'],
        $data['price_includes_tax'],
        $data['tax_rate'],
        $data['is_active'],
        $data['id']
    );
    
    if($stmt->execute()) {
        redirect("../views/products/");
    };
}

function delete_product($id) {
    global $conn;
    global $company_id;
    return $conn->query("DELETE FROM sales_products WHERE id = $id AND company_id = $company_id");
}
