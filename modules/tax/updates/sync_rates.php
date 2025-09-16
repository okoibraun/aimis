<?php
session_start();
include_once '../../../config/db.php';
include("../../../functions/role_functions.php");
include_once '../functions/log_event.php';

$country = $_POST['country'];
if($country == "Nigeria") {
    $source = 'Nigeria Tax Rates'; // This can be replaced with external source name
    $status = 'success';
    $rate_details = "VAT: 7.5%, WHT: 10%"; // Simulated values
} else {
    $source = "{$country} Tax Rate"; // This can be replaced with external source name
    $status = 'success';
    $rate_details = "VAT: 15%, WHT: 15%"; // Simulated values
}

$stmt = $conn->prepare("INSERT INTO tax_rate_updates (company_id, country, source, status, rate_details) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $company_id, $country, $source, $status, $rate_details);
if($stmt->execute()) {
    // Log the event
    log_tax_event($conn, $company_id, $user_id, 'tax', 'RATES_SYNCED', 'tax_rate_updates', $conn->insert_id, "Synced rates for {$country}: {$rate_details}");
    // Successfully inserted
    $_SESSION['success'] = "Tax rates for $country synced successfully.";
    header("Location: ./");
    exit;
} else {
    // Error occurred
    $_SESSION['error'] = "Failed to sync tax rates for $country: " . $stmt->error;
    header("Location: ./");
    exit;
}

// OPTIONAL: Update actual tax_config table if auto-sync is desired
// Example: update VAT rate globally for country X here

// $stmt = $pdo->prepare("UPDATE tax_config SET vat_rate = 15, wht_rate = 7.5 WHERE country = ?");
// $stmt->bind_param("s", $country);
