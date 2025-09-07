<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch document
$doc = $conn->query("SELECT d.*, u.name AS uploader, f.name AS folder_name 
                       FROM documents d
                       LEFT JOIN users u ON d.uploaded_by = u.id
                       LEFT JOIN document_folders f ON d.folder_id = f.id
                       WHERE d.id = $id")->fetch_assoc();

if (!$doc) {
    echo "<div class='alert alert-danger'>Document not found.</div>";
    require_once '../../includes/footer.phtml';
    exit;
}

// Fetch versions
$versions = $conn->query("SELECT * FROM document_versions WHERE document_id = $id ORDER BY version_number DESC");
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
                <?php if (isset($_GET['approved'])): ?>
                    <div class="alert alert-success">Document approved successfully.</div>
                <?php elseif (isset($_GET['rejected'])): ?>
                    <div class="alert alert-warning">Document rejected.</div>
                <?php elseif (isset($_GET['updated'])): ?>
                    <div class="alert alert-info">Document updated.</div>
                <?php endif; ?>
                <?php if (isset($_GET['version_uploaded'])): ?>
                    <div class="alert alert-success">New document version uploaded successfully.</div>
                <?php endif; ?>
                <?php if (isset($_GET['ocr'])): ?>
                    <div class="alert alert-info">OCR text extracted and updated successfully.</div>
                <?php endif; ?>

                <section class="content-header">
                    <h1>View Document <small><?= htmlspecialchars($doc['title']) ?></small></h1>
                </section>

                <section class="content">
                    <div class="row">
                        <!-- Document Details -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header"><strong>Document Preview</strong></div>
                                <div class="card-body">
                                    <?php if (in_array($doc['file_type'], ['application/pdf'])): ?>
                                        <iframe src="../../<?= $doc['file_path'] ?>" width="100%" height="500px"></iframe>
                                    <?php elseif (str_starts_with($doc['file_type'], 'image/')): ?>
                                        <img src="../../<?= $doc['file_path'] ?>" class="img-fluid">
                                    <?php else: ?>
                                        <p><a href="../../<?= $doc['file_path'] ?>" target="_blank">Download File</a></p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header"><strong>OCR Text</strong></div>
                                <div class="card-body">
                                    <pre style="white-space: pre-wrap;"><?= htmlspecialchars($doc['ocr_text'] ?: 'No OCR text available.') ?></pre>
                                </div>
                            </div>
                        </div>

                        <!-- Metadata & Workflow -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header"><strong>Metadata</strong></div>
                                <div class="card-body">
                                    <p><strong>Title:</strong> <?= htmlspecialchars($doc['title']) ?></p>
                                    <p><strong>Folder:</strong> <?= htmlspecialchars($doc['folder_name'] ?? '-') ?></p>
                                    <p><strong>Tags:</strong> <?= htmlspecialchars($doc['tags']) ?></p>
                                    <p><strong>Status:</strong> <span class="badge badge-<?= getStatusColor($doc['status']) ?>"><?= ucfirst($doc['status']) ?></span></p>
                                    <p><strong>Uploader:</strong> <?= htmlspecialchars($doc['uploader']) ?></p>
                                    <p><strong>Uploaded On:</strong> <?= date('Y-m-d H:i', strtotime($doc['created_at'])) ?></p>
                                    <p><strong>Expires:</strong> <?= $doc['expires_at'] ? date('Y-m-d', strtotime($doc['expires_at'])) : '-' ?></p>
                                    <p><strong>Retention:</strong> <?= htmlspecialchars($doc['retention_policy'] ?? '-') ?></p>
                                </div>
                                <div class="card-footer">
                                    <a href="edit.php?id=<?= $doc['id'] ?>" class="btn btn-warning">Edit</a>
                                    <?php if ($doc['status'] === 'pending_approval'): ?>
                                        <a href="approve.php?id=<?= $doc['id'] ?>" class="btn btn-success">Approve</a>
                                        <a href="reject.php?id=<?= $doc['id'] ?>" class="btn btn-danger">Reject</a>
                                    <?php endif; ?>
                                    <a href="upload_new_version.php?doc_id=<?= $doc['id'] ?>" class="btn btn-info">Upload New Version</a>
                                    <a href="ocr.php?id=<?= $doc['id'] ?>" class="btn btn-dark">Run OCR</a>
                                </div>
                            </div>

                            <!-- Version History -->
                            <div class="card mt-3">
                                <div class="card-header"><strong>Version History</strong></div>
                                <div class="card-body">
                                    <?php if ($versions): ?>
                                        <ul class="list-group">
                                            <?php foreach ($versions as $v): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    v<?= $v['version_number'] ?> 
                                                    <a href="../../<?= $v['file_path'] ?>" target="_blank">Download</a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p>No additional versions.</p>
                                    <?php endif; ?>
                                </div>
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

<?php
// Helper again
function getStatusColor($status) {
    return match ($status) {
        'approved' => 'success',
        'pending_approval' => 'warning',
        'rejected' => 'danger',
        'draft' => 'secondary',
        default => 'light'
    };
}
?>
