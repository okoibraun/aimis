<?php

function log_tax_event($pdo, $company_id, $user_id, $module, $event_type, $entity_type, $entity_id, $details) {
  global $conn;
  $ip = $_SERVER['REMOTE_ADDR'] ?? 'N/A';
  $stmt = $conn->prepare("INSERT INTO tax_audit_logs (company_id, user_id, module, event_type, entity_type, entity_id, details, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param('iisssiss', $company_id, $user_id, $module, $event_type, $entity_type, $entity_id, $details, $ip);
  $stmt->execute();
}
