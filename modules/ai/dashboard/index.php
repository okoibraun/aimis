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

// Count stats
$leadScoreCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM ai_logs WHERE feature = 'lead_scoring'"))['total'];
$nlqCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM ai_logs WHERE feature = 'natural_language_query'"))['total'];
$memoCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM ai_memos"))['total'];
$summaryCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM ai_doc_outputs WHERE task_type='summary'"))['total'];
$translateCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM ai_doc_outputs WHERE task_type='translation'"))['total'];
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | AI - Automation</title>
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
                <h1><i class="fas fa-robot"></i> AI Module Dashboard</h1>
              </section>

              <section class="content">
                <div class="row">
                  <!-- Cards for each feature -->
                  <div class="col-md-3">
                    <div class="small-box bg-primary">
                      <div class="inner">
                        <h3><?= $leadScoreCount ?></h3>
                        <p>CRM Lead Scores</p>
                      </div>
                      <div class="icon"><i class="fas fa-bolt"></i></div>
                      <a href="../../crm/ai/lead_scoring.php" class="small-box-footer">Go <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="small-box bg-success">
                      <div class="inner">
                        <h3><?= $nlqCount ?></h3>
                        <p>Natural Language Queries</p>
                      </div>
                      <div class="icon"><i class="fas fa-search"></i></div>
                      <a href="../nlp/nl_query.php" class="small-box-footer">Go <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="small-box bg-warning">
                      <div class="inner">
                        <h3><?= $memoCount ?></h3>
                        <p>Voice/Text Memos</p>
                      </div>
                      <div class="icon"><i class="fas fa-microphone-alt"></i></div>
                      <a href="../input/voice_memo.php" class="small-box-footer">Go <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="small-box bg-danger">
                      <div class="inner">
                        <h3><?= $summaryCount + $translateCount ?></h3>
                        <p>Document AI Tasks</p>
                      </div>
                      <div class="icon"><i class="fas fa-file-alt"></i></div>
                      <a href="../automation/doc_translate.php" class="small-box-footer">Go <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>
                </div>

                <!-- Recent logs -->
                <div class="card card-secondary">
                  <div class="card-header"><h3 class="card-title">Recent AI Logs</h3></div>
                  <div class="card-body table-responsive p-0" style="max-height: 300px;">
                    <table class="table table-sm table-hover table-bordered">
                      <thead><tr><th>Module</th><th>Feature</th><th>Input</th><th>Output</th><th>Time</th></tr></thead>
                      <tbody>
                        <?php
                        $logs = mysqli_query($conn, "SELECT module, feature, input_data, output_data, created_at FROM ai_logs ORDER BY created_at DESC LIMIT 10");
                        while ($log = mysqli_fetch_assoc($logs)) {
                          echo "<tr>
                                  <td>{$log['module']}</td>
                                  <td>{$log['feature']}</td>
                                  <td>" . htmlspecialchars(substr($log['input_data'], 0, 40)) . "</td>
                                  <td>" . htmlspecialchars(substr($log['output_data'], 0, 40)) . "</td>
                                  <td>{$log['created_at']}</td>
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
