<?php
function getSalesForecast($sales_json) {
    // In real usage: send to OpenAI or ML model
    // For now, simulate a response
    return [
        'forecast' => 'Based on the last month, expected sales for the next period will be around 950 units.',
        'confidence' => 87.5
    ];
}

function getLeadScore($lead_json) {
    // Simulated logic – use real AI model or OpenAI later
    $lead = json_decode($lead_json, true);
    $score = rand(60, 95); // fake confidence score
    return ['score' => $score];
}

function interpretNaturalLanguageQuery($query_text) {
    // Stub: Basic keyword matching to generate example SQL
    $sql = "";
    $response = "";

    if (stripos($query_text, 'sales') !== false && stripos($query_text, 'product') !== false) {
        $sql = "SELECT p.name AS product, SUM(sii.quantity * sii.unit_price) AS total_sales
                FROM sales_invoice_items sii
                JOIN sales_invoices si ON sii.invoice_id = si.id
                JOIN sales_products p ON sii.product_id = p.id
                WHERE MONTH(si.invoice_date) = MONTH(CURDATE())
                GROUP BY sii.product_id
                ORDER BY total_sales DESC";
        $response = "Showing total sales by product for the current month.";
    }
    elseif (stripos($query_text, 'top customers') !== false) {
        $sql = "SELECT c.name, SUM(sii.quantity * sii.unit_price) AS total_spent
                FROM sales_invoices si
                JOIN sales_invoice_items sii ON si.id = sii.invoice_id
                JOIN sales_customers c ON si.customer_id = c.id
                GROUP BY si.customer_id
                ORDER BY total_spent DESC
                LIMIT 5";
        $response = "Showing top 5 customers by total sales.";
    }

    return ['sql' => $sql, 'response' => $response];
}

function autoCategorizeDocument($doc_metadata) {
    // Simulated result
    return [
        'category' => 'Invoice',
        'tags' => ['vendor', 'payment', 'finance'],
        'confidence' => 92.4
    ];
}

// function autoCategorizeTransaction($txn) {
//     // Simulated result
//     return [
//         'category' => 'Utilities Expense',
//         'tags' => ['electricity', 'monthly', 'accounts payable'],
//         'confidence' => 88.7
//     ];
// }

function autoCategorizeTransaction($txn_json) {
    // Simulated AI response
    $txn = json_decode($txn_json, true);
    $desc = strtolower($txn['description']);
    $category = 'Uncategorized';
    $tags = [];
    $confidence = rand(80, 98);

    if (strpos($desc, 'electricity') !== false || strpos($desc, 'power') !== false) {
        $category = 'Utilities';
        $tags = ['electricity', 'power', 'monthly'];
    } elseif (strpos($desc, 'flight') !== false || strpos($desc, 'ticket') !== false) {
        $category = 'Travel';
        $tags = ['flight', 'airfare'];
    } elseif (strpos($desc, 'salary') !== false || strpos($desc, 'payroll') !== false) {
        $category = 'Salaries';
        $tags = ['staff', 'monthly payroll'];
    }

    return [
        'category' => $category,
        'tags' => $tags,
        'confidence' => $confidence
    ];
}

function handleInternalFAQ($question) {
    $q = strtolower($question);
    if (strpos($q, 'leave policy') !== false) {
        return "The company leave policy allows 20 paid leave days per year. Submit your leave through the HR portal.";
    } elseif (strpos($q, 'salary') !== false || strpos($q, 'payroll') !== false) {
        return "Salary is paid on the last working day of each month. For payroll issues, contact payroll@yourcompany.com.";
    } elseif (strpos($q, 'travel') !== false && strpos($q, 'advance') !== false) {
        return "To request a travel advance, fill out the Travel Advance Request form and submit it to Finance.";
    }

    return "Sorry, I couldn't find a specific answer to your question. Please contact HR or Accounts for help.";
}

function summarizeDocumentText($text) {
    return "Summary: " . substr($text, 0, 100) . "...";
}

function translateDocumentText($text, $to) {
    return "[Translated to $to]: " . substr($text, 0, 100) . " ...";
}

