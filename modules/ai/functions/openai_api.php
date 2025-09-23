<?php

require_once '../../../config/openai.php';

// function callOpenAI($prompt, $temperature = 0.3) {
//     $apiKey = OPENAI_API_KEY;
//     $model = OPENAI_MODEL;

//     $data = [
//         'model' => $model,
//         'messages' => [
//             [
//                 'role' => 'developer',
//                 'content' => 'You are a helpful assistant.'
//             ],
//             [
//                 'role' => 'user',
//                 'content' => $prompt
//             ]
//         ],
//         'temperature' => $temperature
//     ];

//     $ch = curl_init('https://api.openai.com/v1/chat/completions');
//     curl_setopt_array($ch, [
//         CURLOPT_RETURNTRANSFER => true,
//         CURLOPT_HTTPHEADER => [
//             'Content-Type: application/json',
//             'Authorization: Bearer ' . $apiKey
//         ],
//         CURLOPT_POSTFIELDS => json_encode($data)
//     ]);

//     $response = curl_exec($ch);
//     curl_close($ch);
//     $json = json_decode($response, true);
//     return $json['choices'][0]['message']['content'] ?? '[Error: No response]';
// }

function callOpenAI($prompt, $apiKey = OPENAI_API_KEY) {
    $url = "https://api.openai.com/v1/chat/completions";

    $data = [
        "model" => "gpt-4o-mini", // lightweight, cheap model
        "messages" => [
            ["role" => "system", "content" => "You are a helpful assistant."],
            ["role" => "user", "content" => $prompt]
        ],
        "temperature" => 0.7
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return "cURL Error: " . curl_error($ch);
    }

    curl_close($ch);

    $result = json_decode($response, true);

    return $result["choices"][0]["message"]["content"] ?? "No response";
}

// echo callOpenAI("cmr");
// exit;

// function askChatGPT($prompt, $apiKey = OPENAI_API_KEY) {
//     $url = "https://api.openai.com/v1/chat/completions";

//     $data = [
//         "model" => "gpt-4o-mini", // lightweight, cheap model
//         "messages" => [
//             ["role" => "system", "content" => "You are a helpful assistant."],
//             ["role" => "user", "content" => $prompt]
//         ],
//         "temperature" => 0.7
//     ];

//     $ch = curl_init($url);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, [
//         "Content-Type: application/json",
//         "Authorization: Bearer $apiKey"
//     ]);
//     curl_setopt($ch, CURLOPT_POST, true);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

//     $response = curl_exec($ch);

//     if (curl_errno($ch)) {
//         return "cURL Error: " . curl_error($ch);
//     }

//     curl_close($ch);

//     $result = json_decode($response, true);

//     return $result["choices"][0]["message"]["content"] ?? "No response";
// }

function getLeadScore($lead_json) {
    $prompt = "Analyze this lead JSON and give a conversion likelihood score (0 to 100) only:\n$lead_json";
    $response = callOpenAI($prompt);
    preg_match('/\d{2,3}/', $response, $match);
    return ['score' => intval($match[0] ?? 70)];
}

function getSalesForecast($input) {
    /*
    $input = [
        'period' => 'Q3 2025',
        'region' => 'Lagos',
        'product' => 'Cement',
        'historical' => 'Total sales last quarter: ₦120,000,000; growth rate: 15%; avg unit price: ₦25,000'
    ];
    */

    $context = "Generate a predictive sales forecast based on the following input:\n";
    $context .= "Period: {$input['period']}\n";
    if (!empty($input['region'])) {
        $context .= "Region: {$input['region']}\n";
    }
    if (!empty($input['product'])) {
        $context .= "Product: {$input['product']}\n";
    }
    $context .= "Historical Sales Data:\n{$input['historical']}\n\n";
    $context .= "Forecast sales trends, expected revenue, and highlight potential risks or opportunities.";

    return callOpenAI($context);
}


function interpretNaturalLanguageQuery($query_text) {
    $prompt = "Convert the following question into MySQL query for a sales database:\n\n$query_text\n\nAlso explain briefly what the query will return.";
    $response = callOpenAI($prompt);

    // Parse
    preg_match('/SELECT .*?;/is', $response, $sqlMatch);
    $sql = $sqlMatch[0] ?? '';
    $explanation = preg_replace('/SELECT .*?;/is', '', $response);

    return ['sql' => $sql, 'response' => trim($explanation)];
}

function summarizeMemoText($text) {
    $prompt = "Summarize this memo:\n$text";
    return callOpenAI($prompt);
}

function summarizeDocumentText($text) {
    $prompt = "Summarize this document:\n$text";
    return callOpenAI($prompt);
}

function translateDocumentText($text, $to) {
    $prompt = "Translate the following text to language code '$to':\n$text";
    return callOpenAI($prompt);
}

function handleInternalFAQ($question) {
    $prompt = "You are a chatbot answering employee HR, payroll, accounts and finance-related questions. Answer this:\n$question";
    //return askChatGPT($prompt);
    return callOpenAI($prompt);
}


