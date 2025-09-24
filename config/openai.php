<?php
session_start();

include("db.php");
include("../functions/role_functions.php");

$openai_api_key = $conn->query("SELECT ai_api_key FROM companies WHERE id = $company_id");
define('OPENAI_API_KEY', $openai_api_key);
define('OPENAI_MODEL', 'gpt-5'); // or use 'gpt-4' / 'gpt-3.5-turbo' / gpt-4o
// define('OPENAI_MODEL', 'gpt-5');
?>