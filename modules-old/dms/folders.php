<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

$user_role = $roles;
// Check if user has permission to manage folders
if(!in_array($_SESSION['user_role'], $user_role)) {
    require_once("../../includes/head.phtml");
    echo "<div class='alert alert-danger'>You do not have permission to manage folders.</div>";
    require_once '../../includes/footer.phtml';
    exit;
}

// Handle add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['name']);
    $parent_id = $_POST['parent_id'] ?: null;
    $stmt = $conn->prepare("INSERT INTO document_folders (name, parent_id, created_by) VALUES (?, ?, ?)");
    $stmt->execute([$name, $parent_id, $_SESSION['user_id']]);
    header("Location: folders.php?added=1");
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $conn->prepare("DELETE FROM document_folders WHERE id = ?")->execute([$delete_id]);
    header("Location: folders.php?deleted=1");
    exit;
}

// Handle rename
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'rename') {
    $new_name = trim($_POST['new_name']);
    $folder_id = (int)$_POST['folder_id'];
    $conn->prepare("UPDATE document_folders SET name = ? WHERE id = ?")->execute([$new_name, $folder_id]);
    header("Location: folders.php?renamed=1");
    exit;
}

// Fetch folders
$folders = $conn->query("SELECT * FROM document_folders ORDER BY parent_id ASC, name ASC");
$folder_tree = [];

// Build simple parent-child map
foreach ($folders as $folder) {
    $folder_tree[$folder['parent_id']][] = $folder;
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Documents (DMS) - Manage Folders</title>
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
                    <h1>Manage Document Folders</h1>
                </section>

                <section class="content">
                    <?php if (isset($_GET['added'])): ?><div class="alert alert-success">Folder added.</div><?php endif; ?>
                    <?php if (isset($_GET['deleted'])): ?><div class="alert alert-danger">Folder deleted.</div><?php endif; ?>
                    <?php if (isset($_GET['renamed'])): ?><div class="alert alert-info">Folder renamed.</div><?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">Add Folder</div>
                                <div class="card-body">
                                    <form method="post">
                                        <input type="hidden" name="action" value="add">
                                        <div class="form-group">
                                            <label>Folder Name</label>
                                            <input type="text" name="name" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Parent Folder (optional)</label>
                                            <select name="parent_id" class="form-control">
                                                <option value="">-- None --</option>
                                                <?php foreach ($folders as $f): ?>
                                                    <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <button class="btn btn-primary">Add Folder</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">Folder List</div>
                                <div class="card-body">
                                    <?php renderFolderTree($folder_tree, null, 0); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </section>
            </div>

            <?php require_once '../../templates/footer.php'; ?>

            <?php
            // Recursive function to render folders
            function renderFolderTree($tree, $parent_id = null, $depth = 0) {
                if (!isset($tree[$parent_id])) return;
                foreach ($tree[$parent_id] as $folder) {
                    echo str_repeat("&nbsp;&nbsp;&nbsp;", $depth);
                    echo "📁 <strong>" . htmlspecialchars($folder['name']) . "</strong>";
                    echo " <a href='?delete={$folder['id']}' onclick='return confirm(\"Delete folder?\")' class='btn btn-sm btn-danger'>Delete</a>";
                    echo " <button class='btn btn-sm btn-secondary' onclick=\"renameFolder({$folder['id']}, '{$folder['name']}')\">Rename</button>";
                    echo "<br>";
                    renderFolderTree($tree, $folder['id'], $depth + 1);
                }
            }
            ?>

            <!-- Rename Folder Modal -->
            <div class="modal fade" id="renameModal" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <form method="post" class="modal-content">
                    <input type="hidden" name="action" value="rename">
                    <input type="hidden" name="folder_id" id="renameFolderId">
                    <div class="modal-header"><h5 class="modal-title">Rename Folder</h5></div>
                    <div class="modal-body">
                        <input type="text" name="new_name" id="renameFolderName" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
            </div>

            <script>
            function renameFolder(id, name) {
                document.getElementById('renameFolderId').value = id;
                document.getElementById('renameFolderName').value = name;
                $('#renameModal').modal('show');
            }
            </script>

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
