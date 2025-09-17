<?php
require_once '../../../config/db.php';
require_once '../functions.php';

header('Content-Type: application/json');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$report = getReportById($conn, $id);

if (!$report) {
    echo json_encode(['error' => 'Invalid report ID']);
    exit;
}

// Execute SQL query
$query = $report['query'];
$result = $conn->query($query);

if (!$result) {
    echo json_encode(['error' => 'SQL Error']);
    exit;
}

$labels = [];
$data = [];

foreach ($result as $row) {
    $labels[] = $row[0];
    $data[] = (float)$row[1];
}

$chart_type = $report['type'];
$config = json_decode($report['config'], true) ?? [];

$response = [
    'type' => $chart_type,
    'data' => [
        'labels' => $labels,
        'datasets' => [[
            'label' => $report['name'],
            'data' => $data,
            'backgroundColor' => $config['colors'] ?? 'rgba(60,141,188,0.9)',
            'borderColor' => $config['borderColor'] ?? '#3b8bba',
            'borderWidth' => 1
        ]]
    ],
    'options' => [
        'responsive' => true,
        'plugins' => [
            'legend' => ['position' => 'top'],
            'title' => [
                'display' => true,
                'text' => $report['name']
            ]
        ]
    ]
];

if (isset($config['threshold'])) {
    $response['options']['thresholds'] = $config['threshold'];
}

echo json_encode($response);
