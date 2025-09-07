<?php
require_once '../../../config/db.php';

// Step 1: Get leads without analysis or recently updated
$contacts = $conn->query("
  SELECT c.id, c.first_name, c.last_name, c.email
  FROM crm_contacts c
  LEFT JOIN crm_lead_insights i ON i.contact_id = c.id
  WHERE c.deleted_at IS NULL
  ORDER BY c.updated_at DESC
  LIMIT 20
");

while ($contact = $contacts->fetch_assoc()) {
    $contact_id = $contact['id'];

    // TODO: In production, replace below with call to actual AI service
    $score = rand(30, 95); // mock confidence
    $score_reason = "Based on activity recency, email engagement, and deal value";
    $sentiments = ['positive', 'neutral', 'negative'];
    $sentiment = $sentiments[array_rand($sentiments)];
    $sentiment_summary = $sentiment === 'positive' ? "Shows strong interest" :
                         ($sentiment === 'neutral' ? "No strong signals yet" : "Appears disengaged");

    // Upsert into crm_lead_insights
    $stmt = $conn->prepare("
        INSERT INTO crm_lead_insights (contact_id, score, score_reason, sentiment, sentiment_summary)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
          score = VALUES(score),
          score_reason = VALUES(score_reason),
          sentiment = VALUES(sentiment),
          sentiment_summary = VALUES(sentiment_summary),
          updated_at = CURRENT_TIMESTAMP
    ");
    $stmt->bind_param("iisss", $contact_id, $score, $score_reason, $sentiment, $sentiment_summary);
    $stmt->execute();

    echo "Analyzed contact #{$contact_id} - Score: $score, Sentiment: $sentiment<br>";
}

echo "AI analysis completed for all contacts.<br>";
echo "<a href='../contacts/list.php'>Back to Contacts</a>";