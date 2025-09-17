<?php
function getReportsByRole($conn, $role_id) {
    $stmt = $conn->prepare("SELECT * FROM reports WHERE FIND_IN_SET(?, access_roles)");
    $stmt->bind_param("i", $role_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getUserDashboard($conn, $user_id) {
    $stmt = $conn->prepare("SELECT * FROM report_dashboards WHERE user_id = ? LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getReportById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM reports WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getAllRoles($conn) {
    $result = $conn->query("SELECT id, name FROM roles ORDER BY name");
    return $result;
}

function getReportsByUser($conn, $user_id) {
    // Fetch reports created by the user or accessible by their role
    // $role_id = $_SESSION['role_id'];
    $role_id = 1;
    $stmt = $conn->prepare("SELECT * FROM reports WHERE created_by = ? OR FIND_IN_SET(?, access_roles)");
    $stmt->bind_param("ii", $user_id, $role_id);
    $stmt->execute();
    return $stmt->fetch();
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;

function report_export($conn, $report_id, $format = 'pdf') {
    $report = getReportById($conn, $report_id);
    if (!$report) return false;

    $query = $report['query'];
    $result = $conn->query($query);
    if (!$result) return false;

    $rows = [];
    $headers = [];

    while ($row = $result->fetch_assoc()) {
        if (empty($headers)) {
            $headers = array_keys($row);
        }
        $rows[] = array_values($row);
    }

    $filename = "report_{$report_id}_" . date('Ymd_His');
    $filepath = sys_get_temp_dir() . "/{$filename}." . ($format === 'excel' ? 'xlsx' : ($format === 'pdf' ? 'pdf' : 'csv'));

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
        foreach ($headers as $h) echo "<th>" . htmlspecialchars($h) . "</th>";
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

    return $filepath;
}

function report_schedule($conn, $schedule) {
    $filepath = report_export($conn, $schedule['report_id'], $schedule['format']);
    if (!$filepath || !file_exists($filepath)) return false;

    $report = getReportById($conn, $schedule['report_id']);
    $to = $schedule['recipients'];
    $subject = "Scheduled Report: {$report['name']}";
    $body = "Attached is the scheduled report \"{$report['name']}\".\nGenerated on: " . date('Y-m-d H:i:s');

    $headers = "From: noreply@aimis.local\r\n";
    $uid = md5(uniqid(time()));
    $boundary = "==Multipart_Boundary_x{$uid}x";
    $file_data = chunk_split(base64_encode(file_get_contents($filepath)));
    $filename = basename($filepath);

    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

    $message = "--$boundary\r\n";
    $message .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
    $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $message .= "$body\r\n";
    $message .= "--$boundary\r\n";
    $message .= "Content-Type: application/octet-stream; name=\"$filename\"\r\n";
    $message .= "Content-Transfer-Encoding: base64\r\n";
    $message .= "Content-Disposition: attachment; filename=\"$filename\"\r\n\r\n";
    $message .= $file_data . "\r\n";
    $message .= "--$boundary--";

    $sent = mail($to, $subject, $message, $headers);

    unlink($filepath); // Clean up
    return $sent;
}

function checkReportThreshold($values, $config) {
    $alerts = [];

    if (!isset($config['threshold'])) return $alerts;

    $min = $config['threshold']['min'] ?? null;
    $max = $config['threshold']['max'] ?? null;

    foreach ($values as $i => $value) {
        if (($min !== null && $value < $min) || ($max !== null && $value > $max)) {
            $alerts[] = [
                'index' => $i,
                'value' => $value,
                'status' => ($value < $min) ? 'below' : 'above',
            ];
        }
    }

    return $alerts;
}

