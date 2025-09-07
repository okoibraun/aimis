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

// Run tagging
if (isset($_POST['run_tagging'])) {
    $res = mysqli_query($conn, "SELECT d.id, d.title, d.description FROM dms_documents d ORDER BY d.created_at DESC LIMIT 10");
    while ($row = mysqli_fetch_assoc($res)) {
        $meta = json_encode($row);
        $result = autoCategorizeDocument($meta);
        $category = $result['category'];
        $tags = implode(',', $result['tags']);
        $confidence = $result['confidence'];
        $doc_id = $row['id'];

        // Save AI output
        mysqli_query($conn, "INSERT INTO ai_documents (document_id, ai_task, ai_output, created_by)
                             VALUES ('$doc_id', 'auto_tagging', '".mysqli_real_escape_string($conn, "Category: $category\nTags: $tags")."', 1)");

        // Optional: Update DMS document
        mysqli_query($conn, "UPDATE dms_documents SET category='$category' WHERE id = $doc_id");

        // Log
        mysqli_query($conn, "INSERT INTO ai_logs (module, feature, input_data, output_data, confidence_score, created_by)
                             VALUES ('dms', 'auto_tagging', '".mysqli_real_escape_string($conn, $meta)."',
                             '".mysqli_real_escape_string($conn, $category . ' | ' . $tags)."', '$confidence', 1)");
    }
    $msg = "Auto-categorization complete for recent documents.";
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
                    <h1><i class="fas fa-tags"></i> Auto-Categorize Documents</h1>
                </section>

                <section class="content">
                    <form method="POST">
                    <button type="submit" name="run_tagging" class="btn btn-primary mb-3">Run Auto-Categorization</button>
                    </form>

                    <?php if ($msg): ?>
                    <div class="alert alert-success"><?= $msg ?></div>
                    <?php endif; ?>

                    <div class="card card-info">
                    <div class="card-header"><h3 class="card-title">Recently Categorized</h3></div>
                    <div class="card-body table-responsive p-0" style="max-height: 400px;">
                        <table class="table table-sm table-hover table-bordered">
                        <thead>
                            <tr><th>Doc ID</th><th>Category</th><th>Tags</th><th>Date</th></tr>
                        </thead>
                        <tbody>
                        <?php
                        $res = mysqli_query($conn, "SELECT * FROM ai_documents WHERE ai_task = 'auto_tagging' ORDER BY created_at DESC LIMIT 10");
                        while ($r = mysqli_fetch_assoc($res)) {
                            $output = explode("\n", $r['ai_output']);
                            echo "<tr>
                                    <td>{$r['document_id']}</td>
                                    <td>" . htmlspecialchars($output[0]) . "</td>
                                    <td>" . htmlspecialchars($output[1] ?? '') . "</td>
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
