<?php
session_start();
require_once '../../config/db.php';
include("../../functions/role_functions.php");
require_once '../../functions/reports.php'; // contains helpers

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $chart_type = $_POST['chart_type'] ?? 'bar';
    $created_by = $user_id; // fallback if no session

    // If editing, we expect report_id
    $report_id = isset($_POST['report_id']) ? intval($_POST['report_id']) : null;

    if ($report_id) {
        // Update existing report
        $stmt = $conn->prepare("UPDATE reports SET title=?, description=?, chart_type=? WHERE id=?");
        $stmt->bind_param("sssi", $title, $description, $chart_type, $report_id);
        $stmt->execute();

        // Delete old datasets to reinsert
        $stmt = $conn->prepare("DELETE FROM report_datasets WHERE report_id=?");
        $stmt->bind_param("i", $report_id);
        $stmt->execute();
    } else {
        // Insert new report
        $stmt = $conn->prepare("INSERT INTO reports (title, description, chart_type, created_by) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $title, $description, $chart_type, $created_by);
        $stmt->execute();
        $report_id = $stmt->insert_id;
    }

    // Insert datasets
    if (!empty($_POST['dataset_name'])) {
        $dataset_names = $_POST['dataset_name'];
        $dataset_queries = $_POST['dataset_query'];
        $dataset_colors = $_POST['dataset_color'];
        $dataset_chart_types = $_POST['dataset_chart_type'];

        $stmt = $conn->prepare("INSERT INTO report_datasets (report_id, dataset_name, query, color, chart_type) VALUES (?, ?, ?, ?, ?)");

        for ($i = 0; $i < count($dataset_names); $i++) {
            $name = trim($dataset_names[$i]);
            $query = trim($dataset_queries[$i]);
            $color = $dataset_colors[$i] ?? null;
            $type = $dataset_chart_types[$i] ?? 'bar';

            if ($name && $query) {
                $stmt->bind_param("issss", $report_id, $name, $query, $color, $type);
                $stmt->execute();
            }
        }
    }

    // Redirect back
    header("Location: index.php?msg=Report saved successfully");
    exit;
}
?>
