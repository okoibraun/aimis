<?php

$order_items = $db->fetchAll("SELECT * FROM sales_order_items WHERE order_id = ?", [$order_id]);
foreach ($order_items as $item) {
  $db->query("UPDATE inventory_items SET stock_quantity = stock_quantity - '{$item['quantity']}' WHERE product_id = '{$item['product_id']}'");
}
