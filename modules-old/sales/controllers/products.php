<?php
require_once '../includes/helpers.php';

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
                redirect("../views/products/list.php");
                break;
        }
    }
}

function add_product($data) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO sales_products (name, description, price, discount_type, discount_value, bundle_group_id, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsdii",
        $data['name'],
        $data['description'],
        $data['price'],
        $data['discount_type'],
        $data['discount_value'],
        $data['bundle_group_id'],
        $data['is_active']
    );
    if($stmt->execute()) {
        redirect("../views/products/list.php");
    };
}

function update_product($data) {
    global $conn;
    $stmt = $conn->prepare("UPDATE sales_products SET name = ?, description = ?, price = ?, discount_type = ?, discount_value = ?, bundle_group_id = ?, is_active = ? WHERE id = ?");
    $stmt->bind_param("ssdsdiii",
        $data['name'],
        $data['description'],
        $data['price'],
        $data['discount_type'],
        $data['discount_value'],
        $data['bundle_group_id'],
        $data['is_active'],
        $data['id']
    );
    $stmt->execute();
}

function delete_product($id) {
    delete_row_by_id('sales_products', $id);
}
