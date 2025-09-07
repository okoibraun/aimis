<?php
session_start();
require_once '../../config/db.php';


$doc_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch document
$doc = $conn->query("SELECT * FROM documents WHERE id = $doc_id")->fetch_assoc();

if (!$doc) {
    die("Document not found.");
}

// OPTIONAL: check user permission to run OCR
if(!in_array($_SESSION['user_role'], ['admin', 'superadmin', 'editor', 'system'])) {
    die("Unauthorized access.");
}

// Full server path to file
$filePath = realpath('../../' . $doc['file_path']);

// Only allow OCR on images or PDFs
$mime = $doc['file_type'];
if (!in_array($mime, ['application/pdf', 'image/png', 'image/jpeg'])) {
    die("Unsupported file type for OCR.");
}

// Simulate OCR Result for localhost (Replace with real OCR later)
// $ocr_text = "This is a simulated OCR output for '{$doc['title']}'. Replace with Tesseract or API.";

// Optional: hook Tesseract if available
if (function_exists('shell_exec') && file_exists('/usr/bin/tesseract')) {
    // Ensure Tesseract is installed and accessible
    if (!is_readable($filePath)) {
        die("File not readable for OCR.");
    }

    // Run Tesseract OCR command
    $filePath = escapeshellarg($filePath); // Escape file path for shell command
} else {
    die("Tesseract OCR is not available on this server.");
}

$ocr_text = shell_exec("tesseract " . escapeshellarg($filePath) . " stdout");


// Update document
$stmt = $conn->prepare("UPDATE documents SET ocr_text = ?, updated_at = NOW() WHERE id = ?");
$stmt->execute([$ocr_text, $doc_id]);

header("Location: view.php?id=$doc_id&ocr=1");
exit;
