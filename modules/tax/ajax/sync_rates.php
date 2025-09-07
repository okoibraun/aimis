<?php
session_start();
include_once '../../../config/db.php';
include_once '../functions/log_event.php';

$country = $_POST['country'];
$company_id = $_SESSION['company_id'];
$user_id = $_SESSION['user_id'] ?? null;
$source = 'System Default Sync'; // This can be replaced with external source name
$status = 'success';
$rate_details = "VAT: 15%, WHT: 7.5%"; // Simulated values

$stmt = $conn->prepare("INSERT INTO tax_rate_updates (company_id, country, source, status, rate_details) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $company_id, $country, $source, $status, $rate_details);
if($stmt->execute()) {
    // Log the event
    log_tax_event($conn, $company_id, $user_id, 'tax', 'RATES_SYNCED', 'tax_rate_updates', $conn->insert_id, "Synced rates for {$country}: {$rate_details}");
    // Successfully inserted
    $_SESSION['success'] = "Tax rates for $country synced successfully.";
    header("Location: ../updates.php");
    exit;
} else {
    // Error occurred
    $_SESSION['error'] = "Failed to sync tax rates for $country: " . $stmt->error;
    header("Location: ../updates.php");
    exit;
}

// OPTIONAL: Update actual tax_config table if auto-sync is desired
// Example: update VAT rate globally for country X here

// $stmt = $pdo->prepare("UPDATE tax_config SET vat_rate = 15, wht_rate = 7.5 WHERE country = ?");
// $stmt->bind_param("s", $country);
