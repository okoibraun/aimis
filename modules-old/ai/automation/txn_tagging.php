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
if (isset($_POST['run_txn_tagging'])) {
    $res = mysqli_query($conn, "SELECT id, description, amount, vendor FROM accounting_transactions 
                                 WHERE category IS NULL ORDER BY date DESC LIMIT 10");
    while ($row = mysqli_fetch_assoc($res)) {
        $meta = json_encode($row);
        $result = autoCategorizeTransaction($meta);
        $category = $result['category'];
        $tags = implode(',', $result['tags']);
        $confidence = $result['confidence'];
        $txn_id = $row['id'];

        // Save log
        mysqli_query($conn, "INSERT INTO ai_logs (module, feature, input_data, output_data, confidence_score, created_by)
                             VALUES ('finance', 'txn_categorization', '".mysqli_real_escape_string($conn, $meta)."',
                             '".mysqli_real_escape_string($conn, "Category: $category | Tags: $tags")."', '$confidence', 1)");

        // Update transaction
        mysqli_query($conn, "UPDATE accounting_transactions SET category='$category' WHERE id = $txn_id");
    }

    $msg = "Transaction auto-categorization complete.";
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
                    <h1><i class="fas fa-receipt"></i> Auto-Categorize Transactions</h1>
                </section>

                <section class="content">
                    <form method="POST">
                    <button type="submit" name="run_txn_tagging" class="btn btn-primary mb-3">Run Categorization</button>
                    </form>

                    <?php if ($msg): ?>
                    <div class="alert alert-success"><?= $msg ?></div>
                    <?php endif; ?>

                    <div class="card card-warning">
                    <div class="card-header"><h3 class="card-title">Recently Categorized Transactions</h3></div>
                    <div class="card-body table-responsive p-0" style="max-height: 400px;">
                        <table class="table table-sm table-hover table-bordered">
                        <thead>
                            <tr><th>ID</th><th>Description</th><th>Vendor</th><th>Category</th><th>Amount</th></tr>
                        </thead>
                        <tbody>
                        <?php
                        $res = mysqli_query($conn, "SELECT * FROM accounting_transactions WHERE category IS NOT NULL ORDER BY updated_at DESC LIMIT 10");
                        while ($r = mysqli_fetch_assoc($res)) {
                            echo "<tr>
                                    <td>{$r['id']}</td>
                                    <td>{$r['description']}</td>
                                    <td>{$r['vendor']}</td>
                                    <td>{$r['category']}</td>
                                    <td>{$r['amount']}</td>
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
