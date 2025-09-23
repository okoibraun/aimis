<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');
include("../../../functions/role_functions.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

require_once '../functions/openai_api.php'; // we'll create this next

$response_text = "";
$query_result = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_query = $_POST['query_text'];
    $user_id = 1; // Replace with $_SESSION['user_id'] if using sessions

    // Get AI-generated SQL (simulated or real)
    $ai = interpretNaturalLanguageQuery($user_query); // returns ['sql' => '', 'response' => '']
    $sql = $ai['sql'];
    $response_text = $ai['response'];

    // Run the SQL
    $result_data = [];
    if ($sql) {
        $run = $conn->query($sql);
        if ($run) {
            foreach($run as $row) {
                $result_data[] = $row;
            }

            // Log query
            $stmt = $conn->prepare("INSERT INTO ai_nl_queries (company_id, user_id, query_text, response_text, executed_sql) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisss", $company_id, $user_id, $user_query, $response_text, $sql);
            $stmt->execute();
        } else {
            $error = "Error in SQL execution: " . $conn->error;
        }
    } else {
        $error = "AI did not return a valid SQL query.";
    }
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | AI - NLP</title>
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
                    <h1><i class="fas fa-language"></i> Natural Language Query</h1>
                </section>

                <section class="content">
                    <form method="POST" class="card card-info p-3 mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Ask a Question:</h3>
                            <div class="card-tools">
                                <a href="../" class="btn btn-sm btn-danger">X</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                            <?php elseif ($response_text): ?>
                            <div class="alert alert-success">
                                <strong>AI Response:</strong> <?= $response_text ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <input type="text" name="query_text" class="form-control" placeholder="e.g. Show sales by product this month" required>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary">Submit Query</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <?php if (!empty($result_data)): ?>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Query Result</h3>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-sm table-hover DataTable">
                                <thead>
                                <tr>
                                    <?php foreach (array_keys($result_data[0]) as $col): ?>
                                    <th><?= $col ?></th>
                                    <?php endforeach; ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($result_data as $row): ?>
                                    <tr>
                                    <?php foreach ($row as $cell): ?>
                                        <td><?= $cell ?></td>
                                    <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
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
