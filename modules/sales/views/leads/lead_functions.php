<?php
function get_all_leads($company_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM crm_leads WHERE company_id = ? ORDER BY captured_at DESC");
    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $leads = [];
    while ($row = $result->fetch_assoc()) {
        $leads[] = $row;
    }
    return $leads;
}
