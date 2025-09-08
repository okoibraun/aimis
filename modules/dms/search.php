<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Build search filters
$where = [];
$params = [];

if (!empty($_GET['q'])) {
    $where[] = "(d.title LIKE ? OR d.tags LIKE ? OR d.ocr_text LIKE ?)";
    $q = '%' . $_GET['q'] . '%';
    $params[] = $q; $params[] = $q; $params[] = $q;
}

if (!empty($_GET['folder_id'])) {
    $where[] = "d.folder_id = ?";
    $params[] = $_GET['folder_id'];
}

if (!empty($_GET['status'])) {
    $where[] = "d.status = ?";
    $params[] = $_GET['status'];
}

if (!empty($_GET['date_from']) && !empty($_GET['date_to'])) {
    $where[] = "DATE(d.created_at) BETWEEN ? AND ?";
    $params[] = $_GET['date_from'];
    $params[] = $_GET['date_to'];
}

if (!empty($_GET['expiry'])) {
    if ($_GET['expiry'] == 'active') {
        $where[] = "(d.expires_at IS NULL OR d.expires_at > NOW())";
    } elseif ($_GET['expiry'] == 'expired') {
        $where[] = "d.expires_at <= NOW()";
    }
}

$sql = "SELECT d.*, u.name AS uploader, f.name AS folder_name 
        FROM documents d 
        LEFT JOIN users u ON d.uploaded_by = u.id 
        LEFT JOIN document_folders f ON d.folder_id = f.id";

if ($where) $sql .= " WHERE " . implode(' AND ', $where);
$sql .= " ORDER BY d.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetch();

// Fetch folders for filter dropdown
$folders = $conn->query("SELECT * FROM document_folders ORDER BY name");
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
                    <h1>Search Documents</h1>
                </section>

                <section class="content">
                    <form method="get" class="card card-body mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Keyword</label>
                                <input type="text" name="q" class="form-control" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="title, tag, OCR...">
                            </div>
                            <div class="col-md-3">
                                <label>Folder</label>
                                <select name="folder_id" class="form-control">
                                    <option value="">-- Any --</option>
                                    <?php foreach ($folders as $f): ?>
                                        <option value="<?= $f['id'] ?>" <?= ($_GET['folder_id'] ?? '') == $f['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($f['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">-- Any --</option>
                                    <?php foreach (['draft', 'pending_approval', 'approved', 'rejected'] as $s): ?>
                                        <option value="<?= $s ?>" <?= ($_GET['status'] ?? '') == $s ? 'selected' : '' ?>>
                                            <?= ucfirst($s) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Expiry</label>
                                <select name="expiry" class="form-control">
                                    <option value="">-- Any --</option>
                                    <option value="active" <?= ($_GET['expiry'] ?? '') == 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="expired" <?= ($_GET['expiry'] ?? '') == 'expired' ? 'selected' : '' ?>>Expired</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label>From Date</label>
                                <input type="date" name="date_from" class="form-control" value="<?= $_GET['date_from'] ?? '' ?>">
                            </div>
                            <div class="col-md-3">
                                <label>To Date</label>
                                <input type="date" name="date_to" class="form-control" value="<?= $_GET['date_to'] ?? '' ?>">
                            </div>
                            <div class="col-md-3 align-self-end">
                                <button class="btn btn-primary">Search</button>
                                <a href="search.php" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    <div class="card">
                        <div class="card-header">Search Results</div>
                        <div class="card-body">
                            <?php if ($results): ?>
                                <table class="table table-bordered table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Folder</th>
                                            <th>Status</th>
                                            <th>Tags</th>
                                            <th>Uploader</th>
                                            <th>Uploaded</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $doc): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($doc['title']) ?></td>
                                                <td><?= htmlspecialchars($doc['folder_name']) ?></td>
                                                <td><span class="badge badge-<?= getStatusColor($doc['status']) ?>"><?= ucfirst($doc['status']) ?></span></td>
                                                <td><?= htmlspecialchars($doc['tags']) ?></td>
                                                <td><?= htmlspecialchars($doc['uploader']) ?></td>
                                                <td><?= date('Y-m-d', strtotime($doc['created_at'])) ?></td>
                                                <td><a href="view.php?id=<?= $doc['id'] ?>" class="btn btn-sm btn-info">View</a></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p>No matching documents found.</p>
                            <?php endif; ?>
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
