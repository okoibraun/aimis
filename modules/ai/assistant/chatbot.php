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

$response = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = trim($_POST['user_question']);
    $user_id = 1; // Replace with $_SESSION['user_id'] if available

    if ($question) {
        $response = handleInternalFAQ($question);

        // Log query
        $stmt = $conn->prepare("INSERT INTO ai_logs (module, feature, input_data, output_data, confidence_score, created_by)
                                VALUES ('chatbot', 'internal_faq', ?, ?, ?, ?)");
        $stmt->bind_param("ssdi", $question, $response, $score, $user_id);
        $score = 95.0; // Simulated confidence
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
                <section class="content-header">
                    <h1><i class="fas fa-comment-dots"></i> Internal Help Chatbot (HR & Accounts)</h1>
                </section>

                <section class="content">
                    <form method="POST" class="card card-primary p-3 mb-3">
                    <div class="form-group">
                        <label>Ask a question:</label>
                        <input type="text" name="user_question" class="form-control" placeholder="e.g. What is the payroll date?" required>
                    </div>
                    <button type="submit" class="btn btn-info">Get Answer</button>
                    </form>

                    <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                    <?php elseif ($response): ?>
                    <div class="alert alert-success">
                        <strong>Chatbot:</strong> <?= nl2br(htmlspecialchars($response)) ?>
                    </div>
                    <?php endif; ?>
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
