<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Count stats
$leadScoreCount = $conn->query("SELECT COUNT(*) AS total FROM ai_logs WHERE feature = 'lead_scoring'")->fetch_assoc()['total'];
$aiAssistantCount = $conn->query("SELECT COUNT(*) AS total FROM ai_logs WHERE feature = 'internal_faq'")->fetch_assoc()['total'];
$nlqCount = $conn->query("SELECT COUNT(*) AS total FROM ai_logs WHERE feature = 'natural_language_query'")->fetch_assoc()['total'];
$memoCount = $conn->query("SELECT COUNT(*) AS total FROM ai_memos")->fetch_assoc()['total'];
$summaryCount = $conn->query("SELECT COUNT(*) AS total FROM ai_doc_outputs WHERE task_type='summary'")->fetch_assoc()['total'];
$translateCount = $conn->query("SELECT COUNT(*) AS total FROM ai_doc_outputs WHERE task_type='translation'")->fetch_assoc()['total'];
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

              <div class="content-wrapper">
                <section class="content-header mt-3 mb-3">
                  <h1>
                    <i class="fas fa-robot"></i> 
                    AI Dashboard
                  </h1>
                </section>

                <section class="content">
                  <div class="row mt-4">
                    <!-- Cards for each feature -->
                    <div class="col-md-3">
                      <div class="small-box bg-primary">
                        <div class="inner">
                          <h3><?= $aiAssistantCount ?></h3>
                          <p>AI Assistant</p>
                        </div>
                        <div class="small-box-icon"><i class="fas fa-bolt"></i></div>
                        <a href="/modules/ai/assistant/" class="small-box-footer">Go <i class="fas fa-arrow-circle-right"></i></a>
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="small-box bg-success">
                        <div class="inner">
                          <h3><?= $nlqCount ?></h3>
                          <p>Natural Language Queries</p>
                        </div>
                        <div class="small-box-icon"><i class="fas fa-search"></i></div>
                        <a href="/modules/ai/nlp/" class="small-box-footer">Go <i class="fas fa-arrow-circle-right"></i></a>
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="small-box bg-info">
                        <div class="inner">
                          <h3><?= $memoCount ?></h3>
                          <p>Voice/Text Memos</p>
                        </div>
                        <div class="small-box-icon">
                          <i class="fas fa-microphone-alt"></i>
                        </div>
                        <a href="/modules/ai/input/" class="small-box-footer">
                          Go <i class="fas fa-arrow-circle-right"></i>
                        </a>
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="small-box bg-warning">
                        <div class="inner">
                          <h3><?= $summaryCount + $translateCount ?></h3>
                          <p>Analytics (Sales Forecast)</p>
                        </div>
                        <div class="small-box-icon">
                          <i class="fas fa-chart-line"></i>
                        </div>
                        <a href="/modules/ai/analytics/" class="small-box-footer">Go <i class="fas fa-arrow-circle-right"></i></a>
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
