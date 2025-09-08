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
$page = "list";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$super_roles = super_roles();
$system_roles = system_users();
$departmental_roles = departmental_roles();

if (!in_array($_SESSION['role'], $super_roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$company_id = $_SESSION['company_id'];
$user_id = $_SESSION['user_id'];

// Handle search/filter
$keyword = isset($_GET['q']) ? '%' . $_GET['q'] . '%' : '%';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$folder_filter = isset($_GET['folder']) ? (int)$_GET['folder'] : 0;

// Build query
// $sql = "SELECT d.*, u.name AS uploader, f.name AS folder_name 
//         FROM documents d 
//         LEFT JOIN users u ON d.uploaded_by = u.id
//         LEFT JOIN document_folders f ON d.folder_id = f.id
//         WHERE (d.title LIKE ? OR d.description LIKE ? OR d.tags LIKE ? OR d.ocr_text LIKE ?)";

// $params = [$keyword, $keyword, $keyword, $keyword];
$sql = "SELECT d.*, u.name AS uploader, f.name AS folder_name 
    FROM documents d 
    LEFT JOIN users u ON d.uploaded_by = u.id
    LEFT JOIN document_folders f ON d.folder_id = f.id
    WHERE (d.title LIKE '{$keyword}' OR d.description LIKE '{$keyword}' OR d.tags LIKE '{$keyword}' OR d.ocr_text LIKE '{$keyword}')";

if ($status_filter != '') {
    // $sql .= " AND d.status = ?";
    // $params[] = $status_filter;
    $sql .= " AND d.status = $status_filter";
}
if ($folder_filter > 0) {
    // $sql .= " AND d.folder_id = ?";
    // $params[] = $folder_filter;
    $sql .= " AND d.folder_id = $folder_filter";
}

// $stmt = $conn->query($sql);
// $stmt->bind_param("ssss", $params);
// $stmt->execute();
// $documents = $stmt->fetch();
$documents = $conn->query($sql);

// Helper function
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
                <section class="content-header mt-4 mb-4">
                    <h1>Documents <small>Manage uploaded files</small></h1>
                </section>
                
                <section class="content">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Search Document</h3>
                        </div>
                        <div class="card-header">
                            <form method="get" class="form-inline mb-3">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="Search..." class="form-control mr-2">
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="form-group">
                                            <select name="status" class="form-control mr-2">
                                                <option value="">All Status</option>
                                                <?php foreach (['draft', 'pending_approval', 'approved', 'rejected'] as $status): ?>
                                                    <option value="<?= $status ?>" <?= ($status_filter == $status ? 'selected' : '') ?>><?= ucfirst($status) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="form-group">
                                            <button class="btn btn-primary">Filter</button>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <a href="add.php" class="btn btn-success float-right">+ Upload Document</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Folder</th>
                                        <th>Uploader</th>
                                        <th>Version</th>
                                        <th>Status</th>
                                        <th>Uploaded</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($documents as $doc): ?>
                                        <?php if(in_array($_SESSION['user_role'], $super_roles)) { ?>
                                        <?php if($doc['company_id'] != $company_id) { continue; } ?>
                                        <tr>
                                            <td><?= htmlspecialchars($doc['title']) ?></td>
                                            <td><?= htmlspecialchars($doc['folder_name'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($doc['uploader']) ?></td>
                                            <td>v<?= (int)$doc['version'] ?></td>
                                            <td><span class="badge badge-<?= getStatusColor($doc['status']) ?>"><?= ucfirst($doc['status']) ?></span></td>
                                            <td><?= date('Y-m-d', strtotime($doc['created_at'])) ?></td>
                                            <td>
                                                <a href="view.php?id=<?= $doc['id'] ?>" class="btn btn-sm btn-info">View</a>
                                                <a href="edit.php?id=<?= $doc['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                                <?php if ($doc['status'] == 'pending_approval'): ?>
                                                    <a href="approve.php?id=<?= $doc['id'] ?>" class="btn btn-sm btn-success">Approve</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php } else { ?>
                                        <?php if($doc['uploaded_by'] != $_SESSION['user_id']) { continue; } ?>
                                        <tr>
                                            <td><?= htmlspecialchars($doc['title']) ?></td>
                                            <td><?= htmlspecialchars($doc['folder_name'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($doc['uploader']) ?></td>
                                            <td>v<?= (int)$doc['version'] ?></td>
                                            <td><span class="badge badge-<?= getStatusColor($doc['status']) ?>"><?= ucfirst($doc['status']) ?></span></td>
                                            <td><?= date('Y-m-d', strtotime($doc['created_at'])) ?></td>
                                            <td>
                                                <a href="view.php?id=<?= $doc['id'] ?>" class="btn btn-sm btn-info">View</a>
                                                <a href="edit.php?id=<?= $doc['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                                <?php if ($doc['status'] == 'pending_approval'): ?>
                                                    <a href="approve.php?id=<?= $doc['id'] ?>" class="btn btn-sm btn-success">Approve</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    <?php endforeach; ?>
                                    <?php //if (count($documents) === 0): ?>
                                        <!-- <tr><td colspan="7" class="text-center">No documents found.</td></tr> -->
                                    <?php //endif; ?>
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

?>
