<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');
include("../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Check User Permissions
$page = "edit";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$super_roles = super_roles();

if (!in_array($_SESSION['role'], $super_roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch document
$doc = $conn->query("SELECT * FROM documents WHERE id = $id")->fetch_assoc();

if (!$doc) {
    echo "<div class='alert alert-danger'>Document not found.</div>";
    require_once '../../includes/footer.phtml';
    exit;
}

// Handle form submission
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = $_POST['description'];
    $folder_id = $_POST['folder_id'] ?: null;
    $tags = $_POST['tags'];
    $status = $_POST['status'] ?? 'draft';
    $expires_at = $_POST['expires_at'] ?: null;
    $retention_policy = $_POST['retention_policy'] ?: null;

    $file_updated = false;

    // If a new file is uploaded
    if (isset($_FILES['document']) && $_FILES['document']['error'] === 0) {
        $uploadDir = '../../uploads/documents/';
        $ext = pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION);
        $newName = uniqid() . '.' . $ext;
        $destination = $uploadDir . $newName;

        if (move_uploaded_file($_FILES['document']['tmp_name'], $destination)) {
            $file_path = 'uploads/documents/' . $newName;
            $file_type = mime_content_type($destination);
            $file_updated = true;

            // Save current version into document_versions
            $conn->prepare("INSERT INTO document_versions (document_id, version_number, file_path, file_type, created_by)
                           VALUES (?, ?, ?, ?, ?)")
                ->execute([$id, $doc['version'], $doc['file_path'], $doc['file_type'], $_SESSION['user_id']]);

            // Update main document with new file
            $new_version = $doc['version'] + 1;

            $conn->prepare("UPDATE documents SET file_path = ?, file_type = ?, version = ? WHERE id = ?")
                ->execute([$file_path, $file_type, $new_version, $id]);
        } else {
            $errors[] = "File upload failed.";
        }
    }

    if (!$errors) {
        $stmt = $conn->prepare("UPDATE documents SET title=?, description=?, folder_id=?, tags=?, status=?, expires_at=?, retention_policy=? WHERE id=?");
        $stmt->execute([
            $title, $description, $folder_id, $tags, $status, $expires_at, $retention_policy, $id
        ]);

        echo "<script>window.location='view.php?id=$id&updated=1';</script>";
        exit;
    }
}

// Get folders
//$folders = $conn->query("SELECT * FROM document_folders ORDER BY name ASC");
// Get folders
if(in_array($_SESSION['user_role'], $super_roles)) {
    $folders = $conn->query("SELECT * FROM document_folders WHERE company_id = {$_SESSION['company_id']}");
} else {
    $folders = $conn->query("SELECT * FROM document_folders WHERE company_id = {$_SESSION['company_id']} AND created_by = {$_SESSION['user_id']} ORDER BY name ASC");
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
                    <h1>Edit Document</h1>
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
                                    <label>Title</label>
                                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($doc['title']) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label>Replace File (optional)</label>
                                    <input type="file" name="document" class="form-control-file">
                                    <small>Leave blank to keep current file.</small>
                                </div>

                                <div class="form-group">
                                    <label>Folder</label>
                                    <select name="folder_id" class="form-control">
                                        <option value="">-- None --</option>
                                        <?php foreach ($folders as $f): ?>
                                            <option value="<?= $f['id'] ?>" <?= ($doc['folder_id'] == $f['id'] ? 'selected' : '') ?>>
                                                <?= htmlspecialchars($f['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Tags</label>
                                    <input type="text" name="tags" class="form-control" value="<?= htmlspecialchars($doc['tags']) ?>">
                                </div>

                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control"><?= htmlspecialchars($doc['description']) ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <?php foreach (['draft', 'pending_approval', 'approved', 'rejected'] as $status): ?>
                                            <option value="<?= $status ?>" <?= ($doc['status'] == $status ? 'selected' : '') ?>>
                                                <?= ucfirst($status) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Expiry Date</label>
                                    <input type="date" name="expires_at" class="form-control"
                                        value="<?= $doc['expires_at'] ? date('Y-m-d', strtotime($doc['expires_at'])) : '' ?>">
                                </div>

                                <div class="form-group">
                                    <label>Retention Policy</label>
                                    <input type="text" name="retention_policy" class="form-control" value="<?= $doc['retention_policy'] ?>">
                                </div>

                            </div>
                            <div class="card-footer">
                                <button class="btn btn-primary">Update Document</button>
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
