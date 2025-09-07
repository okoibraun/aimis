<?php
// functions.php
include("../config/db.php"); // Database connection

// Function to sanitize inputs
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

// Function to post a journal entry to ledger
function post_to_ledger($account_id, $date, $description, $debit, $credit) {
    global $conn;

    // Calculate running balance
    $balance = 0;
    $query = "SELECT balance FROM accounts_ledger WHERE account_id = '$account_id' ORDER BY date DESC, id DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $balance = $row['balance'];
    }

    // Update balance
    $balance = $balance + $debit - $credit;

    // Insert ledger entry
    $stmt = "INSERT INTO accounts_ledger (account_id, date, description, debit, credit, balance)
             VALUES ('$account_id', '$date', '$description', '$debit', '$credit', '$balance')";
    mysqli_query($conn, $stmt);
}

// Function to get account name by ID
function get_account_name($account_id) {
    global $conn;
    $query = mysqli_query($conn, "SELECT account_name FROM accounts_chart WHERE id = '$account_id'");
    if ($row = mysqli_fetch_assoc($query)) {
        return $row['account_name'];
    } else {
        return "Unknown Account";
    }
}

// Function to fetch exchange rate
function get_exchange_rate($currency_code) {
    global $conn;
    $query = "SELECT rate_to_usd FROM exchange_rates WHERE currency_code = '$currency_code' ORDER BY rate_date DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    $rate = mysqli_fetch_assoc($result);
    return $rate ? $rate['rate_to_usd'] : 1;  // Default to 1 if no rate found (USD itself)
}

// Function to convert amount to base currency (USD)
function convert_to_base_currency($amount, $currency_code) {
    $exchange_rate = get_exchange_rate($currency_code);
    return $amount * $exchange_rate;
}

function apply_accruals() {
    global $conn;

    $today = date('Y-m-d');

    // Fetch pending accruals
    $query = "SELECT * FROM accruals WHERE status = 'Pending' AND start_date <= '$today' AND end_date >= '$today'";
    $result = mysqli_query($conn, $query);

    while ($accrual = mysqli_fetch_assoc($result)) {
        // Check if accrual needs to be applied based on frequency
        $last_applied_date = $accrual['last_applied_date'] ? $accrual['last_applied_date'] : $accrual['start_date'];
        $should_apply = false;

        if ($accrual['frequency'] == 'Monthly' && date('Y-m', strtotime($last_applied_date)) != date('Y-m', strtotime($today))) {
            $should_apply = true;
        } elseif ($accrual['frequency'] == 'Weekly' && date('Y-W', strtotime($last_applied_date)) != date('Y-W', strtotime($today))) {
            $should_apply = true;
        } elseif ($accrual['frequency'] == 'Daily' && date('Y-m-d', strtotime($last_applied_date)) != date('Y-m-d', strtotime($today))) {
            $should_apply = true;
        }

        // Apply the accrual if needed
        if ($should_apply) {
            $query = "UPDATE accruals SET last_applied_date = '$today', status = 'Applied' WHERE id = '{$accrual['id']}'";
            mysqli_query($conn, $query);
            // Add the accrual to the general ledger
            // Add journal entry for accrual
            $journal_query = "INSERT INTO journal_entries (description, amount, currency, date)
                              VALUES ('Accrued Expense: {$accrual['expense_description']}', '{$accrual['amount']}', '{$accrual['currency']}', '$today')";
            mysqli_query($conn, $journal_query);
        }
    }
}

function apply_depreciation() {
    global $conn;

    $today = date('Y-m-d');

    // Fetch assets for depreciation
    $query = "SELECT * FROM depreciation WHERE start_date <= '$today' AND depreciation_method = 'Straight-Line'";
    $result = mysqli_query($conn, $query);

    while ($asset = mysqli_fetch_assoc($result)) {
        // Calculate annual depreciation
        $annual_depreciation = ($asset['asset_value'] - $asset['salvage_value']) / $asset['useful_life'];

        // Update asset value
        $new_value = $asset['current_value'] - $annual_depreciation;
        if ($new_value < $asset['salvage_value']) {
            $new_value = $asset['salvage_value'];  // Prevent going below salvage value
        }

        // Update asset depreciation
        $update_query = "UPDATE depreciation SET current_value = '$new_value', last_depreciation_date = '$today' WHERE id = '{$asset['id']}'";
        mysqli_query($conn, $update_query);

        // Add depreciation entry to journal
        $journal_query = "INSERT INTO journal_entries (description, amount, currency, date)
                          VALUES ('Depreciation on Asset: {$asset['asset_name']}', '$annual_depreciation', '{$asset['currency']}', '$today')";
        mysqli_query($conn, $journal_query);
    }
}


// Additional functions later: exchange rate lookup, budget checking, etc.
?>
