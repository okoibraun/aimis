<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

$doc_id = isset($_GET['doc_id']) ? (int)$_GET['doc_id'] : 0;

// Get current document
$doc = $conn->query("SELECT * FROM documents WHERE id = $doc_id")->fetch_assoc();

if (!$doc) {
    echo "<div class='alert alert-danger'>Document not found.</div>";
    require_once '../../includes/footer.phtml';
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['new_version']) && $_FILES['new_version']['error'] === 0) {
        $uploadDir = '../../uploads/documents/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $filename = basename($_FILES['new_version']['name']);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $newName = uniqid() . '.' . $ext;
        $destination = $uploadDir . $newName;

        if (move_uploaded_file($_FILES['new_version']['tmp_name'], $destination)) {
            $file_path = 'uploads/documents/' . $newName;
            $file_type = mime_content_type($destination);
            $new_version_number = $doc['version'] + 1;

            // Save current file as archived version
            $conn->prepare("INSERT INTO document_versions (document_id, version_number, file_path, file_type, created_by)
                           VALUES (?, ?, ?, ?, ?)")
                ->execute([
                    $doc['id'], $doc['version'], $doc['file_path'], $doc['file_type'], $_SESSION['user_id']
                ]);

            // Update main document with new version
            $conn->prepare("UPDATE documents SET file_path = ?, file_type = ?, version = ?, updated_at = NOW() WHERE id = ?")
                ->execute([$file_path, $file_type, $new_version_number, $doc_id]);

            echo "<script>window.location='view.php?id=$doc_id&version_uploaded=1';</script>";
            exit;
        } else {
            $errors[] = "Failed to upload file.";
        }
    } else {
        $errors[] = "No file selected or upload error.";
    }
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Documents (DMS)</title>
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
                <section class="content-header">
                    <h1>Upload New Version <small><?= htmlspecialchars($doc['title']) ?></small></h1>
                </section>

                <section class="content">
                    <?php if ($errors): ?>
                        <div class="alert alert-danger">
                            <ul><?php foreach ($errors as $e) echo "<li>$e</li>"; ?></ul>
                        </div>
                    <?php endif; ?>

                    <form method="post" enctype="multipart/form-data">
                        <div class="card">
                            <div class="card-body">
                                <p><strong>Current Version:</strong> v<?= $doc['version'] ?></p>

                                <div class="form-group">
                                    <label>New File Version</label>
                                    <input type="file" name="new_version" class="form-control-file" required>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button class="btn btn-primary">Upload New Version</button>
                                <a href="view.php?id=<?= $doc['id'] ?>" class="btn btn-secondary">Cancel</a>
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
