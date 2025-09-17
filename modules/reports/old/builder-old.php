<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

require_once 'functions.php';

$report_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$editing = $report_id > 0;

// Load report if editing
$report = $editing ? getReportById($conn, $report_id) : null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $query = $_POST['query'];
    $config = $_POST['config'];
    $access_roles = implode(',', $_POST['access_roles']);
    $user_id = $_SESSION['user_id'];
    $company_id = $_SESSION['company_id'];

    if ($editing) {
        $stmt = $conn->prepare("UPDATE reports SET name=?, type=?, query=?, config=?, access_roles=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("sssssi", $name, $type, $query, $config, $access_roles, $report_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO reports (company_id, name, type, query, config, access_roles, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssi", $company_id, $name, $type, $query, $config, $access_roles, $user_id);
    }

    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        $error = "Error saving report.";
    }
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Reports</title>
    <?php include_once("../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">

            <div class="content-wrapper">
                <section class="content-header">
                    <h1><?= $editing ? 'Edit Report' : 'Create New Report' ?></h1>
                </section>

                <section class="content">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="card card-primary">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Report Name</label>
                                    <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($report['name'] ?? '') ?>">
                                </div>

                                <div class="form-group">
                                    <label>Chart Type</label>
                                    <select name="type" class="form-control" required>
                                        <?php
                                        $types = ['bar','line','pie','heatmap','stacked'];
                                        foreach ($types as $t):
                                            $sel = ($report['type'] ?? '') == $t ? 'selected' : '';
                                            echo "<option value=\"$t\" $sel>$t</option>";
                                        endforeach;
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>SQL Query</label>
                                    <textarea name="query" class="form-control" rows="5" required><?= htmlspecialchars($report['query'] ?? '') ?></textarea>
                                    <small>Ensure your query returns two columns: label and value (e.g., `SELECT status, COUNT(*) FROM leads GROUP BY status`)</small>
                                </div>

                                <div class="form-group">
                                    <label>Chart Config (JSON)</label>
                                    <textarea name="config" class="form-control" rows="4"><?= htmlspecialchars($report['config'] ?? '') ?></textarea>
                                    <small>Example: <code>{"threshold": {"min": 10, "max": 100}, "colors": ["#f00", "#0f0"]}</code></small>
                                </div>

                                <div class="form-group">
                                    <label>Access Roles</label>
                                    <select name="access_roles[]" class="form-control" multiple>
                                        <?php
                                        $roles = getAllRoles($conn);
                                        $assigned = explode(',', $report['access_roles'] ?? '');
                                        foreach ($roles as $role) {
                                            $selected = in_array($role['id'], $assigned) ? 'selected' : '';
                                            echo "<option value=\"{$role['id']}\" $selected>{$role['name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="form-group float-end">
                                    <a href="index.php" class="btn btn-default">Cancel</a>
                                    <button class="btn btn-success" type="submit">Save Report</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </section>
            </div>

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
