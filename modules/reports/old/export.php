<?php
require_once '../../config/db.php';
require_once '../../vendor/autoload.php'; // adjust path if needed
require_once 'functions.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$format = $_GET['format'] ?? 'csv';

$report = getReportById($conn, $id);

if (!$report) {
    die("Invalid report ID.");
}

$query = $report['query'];
$result = $conn->query($query);

if (!$result) {
    die("SQL Error.");
}

// Collect data
$rows = [];
$headers = [];

foreach ($result as $row) {
    if (empty($headers)) {
        $headers = array_keys($row);
    }
    $rows[] = array_values($row);
}

// EXPORT TO CSV
if ($format === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="report_' . $id . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, $headers);
    foreach ($rows as $r) {
        fputcsv($out, $r);
    }
    fclose($out);
    exit;
}

// EXPORT TO EXCEL
if ($format === 'excel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->fromArray($headers, null, 'A1');
    $sheet->fromArray($rows, null, 'A2');

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="report_' . $id . '.xlsx"');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

// EXPORT TO PDF
if ($format === 'pdf') {
    ob_start();
    echo "<h3>{$report['name']}</h3>";
    echo "<table border='1' cellpadding='4' cellspacing='0'>";
    echo "<tr>";
    foreach ($headers as $h) {
        echo "<th>" . htmlspecialchars($h) . "</th>";
    }
    echo "</tr>";
    foreach ($rows as $r) {
        echo "<tr>";
        foreach ($r as $cell) {
            echo "<td>" . htmlspecialchars($cell) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    $html = ob_get_clean();

    $pdf = new Dompdf();
    $pdf->loadHtml($html);
    $pdf->setPaper('A4', 'landscape');
    $pdf->render();
    $pdf->stream("report_{$id}.pdf", ["Attachment" => 1]);
    exit;
}

// Default fallback
echo "Unsupported format.";
