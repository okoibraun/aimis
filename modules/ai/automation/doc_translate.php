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

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doc_id = $_POST['document_id'];
    $task_type = $_POST['task_type'];
    $lang_to = $_POST['lang_to'];

    // Fetch file content (simulate OCR/text extract)
    $doc = mysqli_fetch_assoc(mysqli_query($conn, "SELECT title, file_path FROM dms_documents WHERE id = $doc_id"));
    $fake_text = file_exists($doc['file_path']) ? file_get_contents($doc['file_path']) : "Sample document content for ID $doc_id";

    // AI processing
    if ($task_type === 'summary') {
        $output = summarizeDocumentText($fake_text);
        $lang_from = 'en';
    } else {
        $output = translateDocumentText($fake_text, $lang_to);
        $lang_from = 'en';
    }

    // Save to ai_doc_outputs
    $stmt = $conn->prepare("INSERT INTO ai_doc_outputs (document_id, task_type, original_text, ai_output, language_from, language_to)
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $doc_id, $task_type, $fake_text, $output, $lang_from, $lang_to);
    $stmt->execute();

    $msg = "AI task '$task_type' completed for document ID $doc_id.";
}
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
                <h1><i class="fas fa-language"></i> Document Summarization / Translation</h1>
              </section>

              <section class="content">
                <?php if ($msg): ?>
                  <div class="alert alert-success"><?= $msg ?></div>
                <?php endif; ?>

                <form method="POST" class="card card-primary p-3 mb-3">
                  <div class="form-row">
                    <div class="form-group col-md-5">
                      <label>Select Document</label>
                      <select name="document_id" class="form-control" required>
                        <option value="">-- Select Document --</option>
                        <?php
                        $res = mysqli_query($conn, "SELECT id, title FROM dms_documents ORDER BY created_at DESC LIMIT 20");
                        while ($r = mysqli_fetch_assoc($res)) {
                          echo "<option value='{$r['id']}'>{$r['title']}</option>";
                        }
                        ?>
                      </select>
                    </div>
                    <div class="form-group col-md-3">
                      <label>Task Type</label>
                      <select name="task_type" class="form-control" required>
                        <option value="summary">Summarize</option>
                        <option value="translation">Translate</option>
                      </select>
                    </div>
                    <div class="form-group col-md-4">
                      <label>Translate To (only for translation)</label>
                      <input type="text" name="lang_to" class="form-control" placeholder="e.g., fr, es, de">
                    </div>
                  </div>
                  <button type="submit" class="btn btn-primary">Run AI Task</button>
                </form>

                <div class="card card-info">
                  <div class="card-header"><h3 class="card-title">Recent AI Outputs</h3></div>
                  <div class="card-body table-responsive p-0" style="max-height: 300px;">
                    <table class="table table-bordered table-sm table-hover">
                      <thead><tr><th>Doc ID</th><th>Task</th><th>Lang</th><th>Excerpt</th><th>Date</th></tr></thead>
                      <tbody>
                        <?php
                        $res = mysqli_query($conn, "SELECT * FROM ai_doc_outputs ORDER BY created_at DESC LIMIT 10");
                        while ($r = mysqli_fetch_assoc($res)) {
                          echo "<tr>
                                  <td>{$r['document_id']}</td>
                                  <td>{$r['task_type']}</td>
                                  <td>{$r['language_from']} → {$r['language_to']}</td>
                                  <td>" . htmlspecialchars(substr($r['ai_output'], 0, 60)) . "...</td>
                                  <td>{$r['created_at']}</td>
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
