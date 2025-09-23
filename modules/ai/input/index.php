<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');
include("../../../functions/role_functions.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

require_once '../functions/openai_api.php'; // we'll create this next

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = 1; // use session in real use
    $raw_text = trim($_POST['dictated_text']);

    if ($raw_text) {
        $summary = summarizeMemoText($raw_text); // from helper

        // Save
        $stmt = $conn->prepare("INSERT INTO ai_memos (company_id, user_id, raw_text, summarized_text) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $company_id, $user_id, $raw_text, $summary);
        $stmt->execute();

        $msg = "Memo saved and summarized successfully.";
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
                        <i class="fas fa-microphone-alt"></i> Summarize using <small>Text/Voice Dictation</small> 
                    </h1>
                </section>

                <section class="content">
                    <?php if ($msg): ?>
                    <div class="alert alert-success"><?= $msg ?></div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col">
                            <form method="POST" class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Dictate of Type in</h3>
                                    <!-- <label>Dictate or type your memo:</label> -->
                                    <div class="card-tools">
                                        <a href="../" class="btn btn-sm btn-danger">X</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <textarea name="dictated_text" id="dictated_text" rows="4" class="form-control" required></textarea>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="form-group float-end">
                                        <button type="button" onclick="startDictation()" class="btn btn-secondary">
                                            <i class="fas fa-microphone"></i> Start Voice Input
                                        </button>
                                        <button type="submit" class="btn btn-primary">Save &amp; Summarize</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="col">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Recent AI Memos</h3>
                                </div>
                                <!-- <div class="card-body table-responsive p-0" style="max-height: 300px;"> -->
                                <div class="card-body table-responsive">
                                    <table class="table table-sm table-bordered table-hover DataTable">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Summary</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $res = $conn->query("SELECT summarized_text, created_at FROM ai_memos ORDER BY created_at DESC LIMIT 10");
                                                foreach($res as $r) { 
                                            ?>
                                            <tr>
                                                <td><?= $r['created_at'] ?></td>
                                                <td><?= $r['summarized_text'] ?></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>


                </section>
            </div>

            <script>
                function startDictation() {
                    if (!('webkitSpeechRecognition' in window)) {
                        alert("Your browser does not support speech recognition.");
                        return;
                    }

                    const recognition = new webkitSpeechRecognition();
                    recognition.lang = "en-US";
                    recognition.interimResults = false;
                    recognition.maxAlternatives = 1;

                    recognition.onresult = function(event) {
                        const transcript = event.results[0][0].transcript;
                        document.getElementById('dictated_text').value += ' ' + transcript;
                    };

                    recognition.onerror = function(event) {
                        alert("Speech recognition error: " + event.error);
                    };

                    recognition.start();
                }
            </script>

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
