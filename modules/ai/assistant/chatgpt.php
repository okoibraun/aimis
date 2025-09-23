<?php
require_once '../functions/openai_api.php'; // we'll create this next

// API endpoint for Chat Completions
$url = "https://api.openai.com/v1/chat/completions";

// The message(s) you want to send
$data = [
    "model" => "gpt-4o-mini",  // you can also use "gpt-4o", "gpt-4.1", etc.
    "messages" => [
        ["role" => "system", "content" => "You are a helpful assistant."],
        ["role" => "user", "content" => "Write a PHP function that reverses a string."]
    ],
    "temperature" => 0.7
];

// Initialize cURL
$ch = curl_init($url);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $apiKey"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Execute and fetch response
$response = curl_exec($ch);

// Handle errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
    exit;
}

curl_close($ch);

// Decode JSON response
$result = json_decode($response, true);

// Print the assistant's reply
echo $result["choices"][0]["message"]["content"] ?? "No response";
?>
