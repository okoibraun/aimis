<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

require_once '../functions/openai_api.php'; // we'll create this next

// Run scoring
if (isset($_POST['run_score'])) {
  $company_id = $_SESSION['company_id'];
    $leads = [];
    // $sql = "SELECT l.id, l.name, l.status, l.industry, l.source, COUNT(a.id) as activity_count
    //         FROM crm_leads l
    //         LEFT JOIN crm_activities a ON l.id = a.lead_id
    //         GROUP BY l.id";
    $sql = "SELECT l.id, l.full_name, l.status, l.industry, l.source, COUNT(a.id) as activity_count
            FROM crm_leads l
            LEFT JOIN crm_activities a ON l.id = a.lead_id
            GROUP BY l.id";
    $res = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($res)) {
        $leads[] = $row;
    }

    foreach ($leads as $lead) {
        $input = json_encode($lead);
        $result = getLeadScore($input); // <- AI call
        $score = $result['score'];

        // Option A: Save into crm_leads
        mysqli_query($conn, "UPDATE crm_leads SET company_id = '{$company_id}', ai_score = '$score' WHERE id = {$lead['id']}");

        // Option B: Log into ai_predictions
        mysqli_query($conn, "INSERT INTO ai_predictions (company_id, prediction_type, reference_id, prediction_result, prediction_score, predicted_for_period)
                             VALUES ('{$company_id}', 'lead_score', {$lead['id']}, '{$score}', '{$score}', CURDATE())");

        // Log entry
        mysqli_query($conn, "INSERT INTO ai_logs (company_id, module, feature, input_data, output_data, confidence_score, created_by)
                             VALUES ('{$company_id}', 'crm', 'lead_scoring', '".mysqli_real_escape_string($conn, $input)."',
                             '{$score}', '{$score}', 1)");
    }

    $message = "Lead scoring complete. AI scores updated.";
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | AI</title>
    <?php include_once("../../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">

            <div class="content-wrapper">
                <section class="content-header">
                    <h1><i class="fas fa-star-half-alt"></i> AI Lead Scoring</h1>
                </section>

                <section class="content">
                    <form method="POST">
                    <button type="submit" name="run_score" class="btn btn-success mb-3">Run AI Scoring</button>
                    </form>

                    <?php if (isset($message)): ?>
                    <div class="alert alert-success"><?= $message ?></div>
                    <?php endif; ?>

                    <div class="card card-primary">
                    <div class="card-header"><h3 class="card-title">Leads & Scores</h3></div>
                    <div class="card-body table-responsive p-0" style="max-height: 400px;">
                        <table class="table table-sm table-hover table-bordered">
                        <thead>
                            <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Industry</th>
                            <th>Source</th>
                            <th>Score</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $res = mysqli_query($conn, "SELECT * FROM crm_leads ORDER BY ai_score DESC");
                        while ($r = mysqli_fetch_assoc($res)) {
                            echo "<tr>
                                    <td>{$r['id']}</td>
                                    <td>{$r['full_name']}</td>
                                    <td>{$r['status']}</td>
                                    <td>{$r['industry']}</td>
                                    <td>{$r['source']}</td>
                                    <td><strong>{$r['ai_score']}%</strong></td>
                                    </tr>";
                        }
                        ?>
                        </tbody>
                        </table>
                    </div>
                    </div>
                </section>
            </div>

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
