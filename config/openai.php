<?php
include("db.php");
$company_id = $_SESSION['company_id'];

$openai_api_key = $conn->query("SELECT ai_api_key FROM companies WHERE id = $company_id")->fetch_assoc();
define('OPENAI_API_KEY', $openai_api_key['ai_api_key']);
define('OPENAI_MODEL', 'gpt-5'); // or use 'gpt-4' / 'gpt-3.5-turbo' / gpt-4o
// define('OPENAI_MODEL', 'gpt-5');
?>