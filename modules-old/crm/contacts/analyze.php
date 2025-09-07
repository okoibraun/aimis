<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';
require_once '../../../vendor/autoload.php'; // if using composer

use OpenAI\Client;

// === Configuration ===
$openai_api_key = 'your-api-key'; // Replace with actual secret or env
$openai = OpenAI::client($openai_api_key);

// === Fetch Leads for Analysis ===
$sql = "SELECT id, description FROM crm_contacts 
        WHERE company_id = {$_SESSION['company_id']} AND deleted_at IS NULL";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $contact_id = $row['id'];
    $text = $row['description'];

    if (!$text) continue;

    // === OpenAI API Request ===
    $prompt = "
You are an AI CRM assistant. Analyze the following lead description and return:
- A lead score (0–100) based on conversion likelihood
- Sentiment (positive, neutral, negative)

Respond ONLY in JSON with keys: score, sentiment.

Lead description:
\"\"\"$text\"\"\"
";

    try {
        $response = $openai->chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'You are an AI CRM analysis assistant.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.4,
        ]);

        $content = $response['choices'][0]['message']['content'];
        $data = json_decode($content, true);

        if (is_array($data) && isset($data['score'], $data['sentiment'])) {
            $score = intval($data['score']);
            $sentiment = strtolower($data['sentiment']);

            // === Save or Update ===
            $stmt = $conn->prepare("INSERT INTO crm_lead_insights (contact_id, score, sentiment, updated_at)
                                    VALUES (?, ?, ?, NOW())
                                    ON DUPLICATE KEY UPDATE score = VALUES(score), sentiment = VALUES(sentiment), updated_at = NOW()");
            $stmt->bind_param("iis", $contact_id, $score, $sentiment);
            $stmt->execute();
        }

    } catch (Exception $e) {
        error_log("AI Error for contact #$contact_id: " . $e->getMessage());
    }
}

echo "Analysis complete.";
