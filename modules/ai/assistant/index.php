<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');
include("../../../functions/role_functions.php");
require_once '../functions/openai_api.php'; // we'll create this next

if (!isset($user_id)) {
    header('Location: /login.php');
    exit();
}


$response = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = trim($_POST['user_question']);
    $module = "chatbot";
    $feature = "internal_faq";
    $score = 95.0; // Simulated confidence


    if ($question) {
        $response = handleInternalFAQ($question);

        // Log query
        $stmt = $conn->prepare("INSERT INTO ai_logs (company_id, module, feature, input_data, output_data, confidence_score, created_by)
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssdi", $company_id, $module, $feature, $question, $response, $score, $user_id);
        $stmt->execute();
    } else {
        $error = "Please enter a question.";
    }
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | AI - Assistant</title>
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
                <section class="content-header mt-3 mb-3">
                    <h1>
                      <i class="fas fa-comment-dots"></i> 
                      Internal Help Chatbot (Payroll & Accounts)
                    </h1>
                </section>

                <section class="content">
                    <form method="POST" class="card">
                      <div class="card-header">
                        <h3 class="card-title">Ask a Question</h3>
                        <div class="card-tools">
                            <a href="../" class="btn btn-sm btn-danger">X</a>
                        </div>
                      </div>
                      <div class="card-body">
                        <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                        <?php elseif ($response): ?>
                        <div class="alert alert-success">
                            <strong>AIMIS ChatBOT:</strong>
                            <p><?= nl2br(htmlspecialchars($response)) ?></p>
                        </div>
                        <?php endif; ?>
                      </div>
                      <div class="card-footer">
                        <div class="row">
                          <div class="col">
                            <div class="form-group">
                                <input type="text" name="user_question" class="form-control" placeholder="e.g. What is the payroll date?" required>
                            </div>
                          </div>
                          <div class="col-auto">
                            <button type="submit" class="btn btn-success">Get Answer</button>
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
