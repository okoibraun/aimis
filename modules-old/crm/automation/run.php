<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

$now = date('Y-m-d H:i:s');
$today = date('Y-m-d');
$log = [];

// Fetch active rules
$rules = $conn->query("SELECT * FROM crm_automation_rules WHERE is_active = 1");

while ($rule = $rules->fetch_assoc()) {
    $company_id = intval($rule['company_id']);
    $trigger_type = $rule['trigger_type'];
    $trigger_value = $rule['trigger_value'];
    $action_type = $rule['action_type'];
    $action_value = $rule['action_value'];

    if ($trigger_type == 'contact_inactivity') {
        // Example: trigger_value = "7_days"
        $days = intval(str_replace('_days', '', $trigger_value));
        $cutoff = date('Y-m-d H:i:s', strtotime("-$days days"));

        $contacts = $conn->query("
            SELECT c.id, c.assigned_to 
            FROM crm_contacts c
            LEFT JOIN (
                SELECT contact_id, MAX(created_at) AS last_activity
                FROM crm_activities
                GROUP BY contact_id
            ) a ON c.id = a.contact_id
            WHERE c.company_id = $company_id
              AND (a.last_activity IS NULL OR a.last_activity < '$cutoff')
        ");

        while ($contact = $contacts->fetch_assoc()) {
            create_reminder($contact['assigned_to'], $company_id, "Follow up with inactive contact #{$contact['id']}", 'contact', $contact['id']);
            $log[] = "Reminder created for contact #{$contact['id']}";
        }
    }

    if ($trigger_type == 'deal_stage_change') {
        // Example: trigger_value = "stage:negotiation"
        list($key, $stage) = explode(':', $trigger_value);

        $deals = $conn->query("
            SELECT id, assigned_to 
            FROM crm_deals 
            WHERE company_id = $company_id AND stage = '$stage' AND updated_at >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)
        ");

        while ($deal = $deals->fetch_assoc()) {
            create_reminder($deal['assigned_to'], $company_id, "Deal moved to $stage stage - Deal #{$deal['id']}", 'deal', $deal['id']);
            $log[] = "Reminder created for deal #{$deal['id']}";
        }
    }

    if ($trigger_type == 'campaign_sent') {
        // Example use-case: send reminder to review campaign follow-up
        $campaigns = $conn->query("
            SELECT id, created_by 
            FROM crm_campaigns 
            WHERE company_id = $company_id 
              AND sent_at >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)
        ");

        while ($campaign = $campaigns->fetch_assoc()) {
            create_reminder($campaign['created_by'], $company_id, "Review follow-up for campaign #{$campaign['id']}", 'campaign', $campaign['id']);
            $log[] = "Reminder created for campaign #{$campaign['id']}";
        }
    }
}

// Output log (for dev/test only)
echo "<pre>";
echo "Automation run completed at $now\n\n";
foreach ($log as $line) echo "$line\n";
echo "</pre>";

/**
 * Create reminder if one doesn't already exist
 */
function create_reminder($user_id, $company_id, $text, $type, $id) {
    global $conn;

    // Avoid duplicate reminders
    $stmt = $conn->prepare("SELECT id FROM crm_reminders WHERE company_id=? AND user_id=? AND related_type=? AND related_id=? AND is_done=0");
    $stmt->bind_param("iisi", $company_id, $user_id, $type, $id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) return; // Exists already

    // Insert new reminder
    $due_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
    $stmt = $conn->prepare("INSERT INTO crm_reminders (company_id, user_id, reminder_text, due_at, is_done, related_type, related_id) VALUES (?, ?, ?, ?, 0, ?, ?)");
    $stmt->bind_param("iisssi", $company_id, $user_id, $text, $due_at, $type, $id);
    $stmt->execute();
}
