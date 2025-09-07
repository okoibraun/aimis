<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}


?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | AI</title>
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

            <!-- Content Wrapper -->
            <div class="content-wrapper">
              <section class="content-header">
                <h1><i class="fas fa-brain"></i> AI Module Dashboard</h1>
              </section>

              <section class="content">

                <div class="row">
                  <!-- Predictions Summary -->
                  <div class="col-md-6">
                    <div class="card card-primary">
                      <div class="card-header"><h3 class="card-title">Recent AI Predictions</h3></div>
                      <div class="card-body table-responsive p-0" style="max-height: 300px;">
                        <table class="table table-sm table-hover table-bordered">
                          <thead>
                            <tr>
                              <th>Type</th>
                              <th>Ref ID</th>
                              <th>Result</th>
                              <th>Score</th>
                              <th>Date</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?php
                          $sql = "SELECT * FROM ai_predictions ORDER BY created_at DESC LIMIT 10";
                          $res = mysqli_query($conn, $sql);
                          while ($row = mysqli_fetch_assoc($res)) {
                              echo "<tr>
                                      <td>{$row['prediction_type']}</td>
                                      <td>{$row['reference_id']}</td>
                                      <td>{$row['prediction_result']}</td>
                                      <td>{$row['prediction_score']}</td>
                                      <td>{$row['created_at']}</td>
                                    </tr>";
                          }
                          ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>

                  <!-- Alerts Summary -->
                  <div class="col-md-6">
                    <div class="card card-danger">
                      <div class="card-header"><h3 class="card-title">Recent AI Alerts</h3></div>
                      <div class="card-body table-responsive p-0" style="max-height: 300px;">
                        <table class="table table-sm table-hover table-bordered">
                          <thead>
                            <tr>
                              <th>Type</th>
                              <th>Module</th>
                              <th>Ref ID</th>
                              <th>Severity</th>
                              <th>Date</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?php
                          $sql = "SELECT * FROM ai_alerts ORDER BY created_at DESC LIMIT 10";
                          $res = mysqli_query($conn, $sql);
                          while ($row = mysqli_fetch_assoc($res)) {
                              echo "<tr>
                                      <td>{$row['alert_type']}</td>
                                      <td>{$row['related_module']}</td>
                                      <td>{$row['reference_id']}</td>
                                      <td><span class='badge badge-{$row['severity']}'>{$row['severity']}</span></td>
                                      <td>{$row['created_at']}</td>
                                    </tr>";
                          }
                          ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Second row for documents and NL queries -->
                <div class="row mt-3">
                  <!-- Document AI Tasks -->
                  <div class="col-md-6">
                    <div class="card card-success">
                      <div class="card-header"><h3 class="card-title">Document AI Tasks</h3></div>
                      <div class="card-body table-responsive p-0" style="max-height: 300px;">
                        <table class="table table-sm table-hover table-bordered">
                          <thead>
                            <tr>
                              <th>Doc ID</th>
                              <th>Task</th>
                              <th>Language</th>
                              <th>Date</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?php
                          $sql = "SELECT * FROM ai_documents ORDER BY created_at DESC LIMIT 10";
                          $res = mysqli_query($conn, $sql);
                          while ($row = mysqli_fetch_assoc($res)) {
                              echo "<tr>
                                      <td>{$row['document_id']}</td>
                                      <td>{$row['ai_task']}</td>
                                      <td>{$row['language']}</td>
                                      <td>{$row['created_at']}</td>
                                    </tr>";
                          }
                          ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>

                  <!-- Natural Language Queries -->
                  <div class="col-md-6">
                    <div class="card card-info">
                      <div class="card-header"><h3 class="card-title">Natural Language Queries</h3></div>
                      <div class="card-body table-responsive p-0" style="max-height: 300px;">
                        <table class="table table-sm table-hover table-bordered">
                          <thead>
                            <tr>
                              <th>User</th>
                              <th>Query</th>
                              <th>Date</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?php
                          $sql = "SELECT q.*, u.name AS user_name FROM ai_nl_queries q
                                  LEFT JOIN users u ON q.user_id = u.id
                                  ORDER BY q.created_at DESC LIMIT 10";
                          $res = mysqli_query($conn, $sql);
                          while ($row = mysqli_fetch_assoc($res)) {
                              echo "<tr>
                                      <td>{$row['user_name']}</td>
                                      <td>{$row['query_text']}</td>
                                      <td>{$row['created_at']}</td>
                                    </tr>";
                          }
                          ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>

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
