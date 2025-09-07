<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = $_POST['description'];
    $folder_id = $_POST['folder_id'] ?: null;
    $tags = $_POST['tags'];
    $status = $_POST['status'] ?? 'draft';
    $expires_at = $_POST['expires_at'] ?: null;
    $retention_policy = $_POST['retention_policy'] ?: null;
    $uploaded_by = $_SESSION['user_id'];
    $company_id = $_SESSION['company_id'] ?? null;

    // File Upload
    if (isset($_FILES['document']) && $_FILES['document']['error'] === 0) {
        $uploadDir = '../../uploads/documents/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $filename = basename($_FILES['document']['name']);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $newName = uniqid() . '.' . $ext;
        $destination = $uploadDir . $newName;

        if (move_uploaded_file($_FILES['document']['tmp_name'], $destination)) {
            $file_path = 'uploads/documents/' . $newName;
            $file_type = mime_content_type($destination);
            $ocr_text = '(OCR placeholder text)'; // Replace later with actual OCR logic

            // Insert into DB
            $stmt = $conn->prepare("INSERT INTO documents (company_id, title, description, file_path, file_type, folder_id, tags, ocr_text, uploaded_by, status, expires_at, retention_policy) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $company_id, $title, $description, $file_path, $file_type, $folder_id, $tags, $ocr_text,
                $uploaded_by, $status, $expires_at, $retention_policy
            ]);

            echo "<script>window.location='list.php?success=1';</script>";
            exit;
        } else {
            $errors[] = "Failed to upload file.";
        }
    } else {
        $errors[] = "No file uploaded or upload error.";
    }
}

// Get folders
$folders = $conn->query("SELECT * FROM document_folders ORDER BY name ASC");
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
                    <h1>Upload Document</h1>
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

                                <div class="form-group">
                                    <label>Document Title</label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label>File Upload</label>
                                    <input type="file" name="document" class="form-control-file" required>
                                </div>

                                <div class="form-group">
                                    <label>Folder</label>
                                    <select name="folder_id" class="form-control">
                                        <option value="">-- None --</option>
                                        <?php foreach ($folders as $f): ?>
                                            <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Tags (comma-separated)</label>
                                    <input type="text" name="tags" class="form-control" placeholder="invoice,contract,scan">
                                </div>

                                <div class="form-group">
                                    <label>Description (optional)</label>
                                    <textarea name="description" class="form-control"></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="draft">Draft</option>
                                        <option value="pending_approval">Submit for Approval</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Expiry Date (optional)</label>
                                    <input type="date" name="expires_at" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label>Retention Policy (optional)</label>
                                    <input type="text" name="retention_policy" class="form-control" placeholder="7 years, permanent, etc.">
                                </div>

                            </div>
                            <div class="card-footer">
                                <button class="btn btn-primary">Upload</button>
                                <a href="list.php" class="btn btn-secondary">Cancel</a>
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
