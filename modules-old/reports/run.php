<?php
require_once '../../config/db.php';
require_once '../../vendor/autoload.php'; // Dompdf + PhpSpreadsheet
require_once 'functions.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;

date_default_timezone_set('Africa/Lagos'); // adjust as needed

// Fetch due schedules
$schedules = $conn->query("SELECT * FROM report_schedules WHERE status = 'active' AND next_run <= NOW()");
foreach($schedules as $schedule) {
    $report = getReportById($conn, $schedule['report_id']);
    if (!$report) continue;

    $query = $report['query'];
    $result = $conn->query($query);
    if (!$result) continue;

    $rows = [];
    $headers = [];
    foreach($result as $row) {
        if (empty($headers)) {
            $headers = array_keys($row);
        }
        $rows[] = array_values($row);
    }

    // Generate file
    $format = $schedule['format'];
    $filename = "report_{$report['id']}_" . date('Ymd_His');
    $filepath = "/tmp/{$filename}." . ($format === 'excel' ? 'xlsx' : ($format === 'pdf' ? 'pdf' : 'csv'));

    if ($format === 'csv') {
        $fp = fopen($filepath, 'w');
        fputcsv($fp, $headers);
        foreach ($rows as $r) fputcsv($fp, $r);
        fclose($fp);
    } elseif ($format === 'excel') {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($headers, null, 'A1');
        $sheet->fromArray($rows, null, 'A2');
        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);
    } elseif ($format === 'pdf') {
        ob_start();
        echo "<h3>{$report['name']}</h3><table border='1' cellpadding='4' cellspacing='0'><tr>";
        foreach ($headers as $h) echo "<th>$h</th>";
        echo "</tr>";
        foreach ($rows as $r) {
            echo "<tr>";
            foreach ($r as $cell) echo "<td>" . htmlspecialchars($cell) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        file_put_contents($filepath, $dompdf->output());
    }

    // Send email
    $to = $schedule['recipients'];
    $subject = "Scheduled Report: {$report['name']}";
    $body = "Attached is the scheduled report: {$report['name']}.\n\nGenerated on: " . date('Y-m-d H:i:s');

    $headers = "From: noreply@aimis.local\r\n";
    $uid = md5(uniqid(time()));
    $boundary = "==Multipart_Boundary_x{$uid}x";
    $file_data = chunk_split(base64_encode(file_get_contents($filepath)));

    $filename_only = basename($filepath);
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

    $message = "--$boundary\r\n";
    $message .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
    $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $message .= "$body\r\n";

    $message .= "--$boundary\r\n";
    $message .= "Content-Type: application/octet-stream; name=\"$filename_only\"\r\n";
    $message .= "Content-Transfer-Encoding: base64\r\n";
    $message .= "Content-Disposition: attachment; filename=\"$filename_only\"\r\n\r\n";
    $message .= $file_data . "\r\n";
    $message .= "--$boundary--";

    mail($to, $subject, $message, $headers);

    // Update schedule
    $now = date('Y-m-d H:i:s');
    $next = getNextRunTime($schedule['frequency']);
    $stmt = $conn->prepare("UPDATE report_schedules SET last_run=?, next_run=? WHERE id=?");
    $stmt->bind_param("ssi", $now, $next, $schedule['id']);
    $stmt->execute();

    unlink($filepath); // cleanup
}

function getNextRunTime($freq) {
    return match ($freq) {
        'daily' => date('Y-m-d H:i:s', strtotime('+1 day')),
        'weekly' => date('Y-m-d H:i:s', strtotime('+1 week')),
        'monthly' => date('Y-m-d H:i:s', strtotime('+1 month')),
        default => date('Y-m-d H:i:s', strtotime('+1 day')),
    };
}
