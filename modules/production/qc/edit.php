<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');
include("../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

// Check User Permissions
$page = "edit";
$user_permissions = get_user_permissions($_SESSION['user_id']);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$id = $_GET['id'];
$q = $conn->query("SELECT * FROM production_qc_checkpoints WHERE id = $id AND company_id = $company_id")->fetch_assoc();

$work_orders = $conn->query("SELECT id, order_code FROM production_work_orders WHERE company_id = $company_id ORDER BY id DESC");
$materials = $conn->query("SELECT id, material FROM production_bom_items WHERE company_id = $company_id");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Production - QC</title>
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
                    <h1>Edit QC Checkpoint</h1>
                </section>

                <section class="content">
                    <form action="save.php" method="post" class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                QC Checkpoint Details
                            </h3>
                            <div class="card-tools">
                                <a href="./" class="btn btn-danger btn-sm">X</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="id" value="<?= $q['id'] ?>">
    
                            <div class="form-group">
                                <label>Work Order</label>
                                <select name="work_order_id" class="form-control" required>
                                    <?php while($w = mysqli_fetch_assoc($work_orders)): ?>
                                        <option value="<?= $w['id'] ?>" <?= $w['id'] == $q['work_order_id'] ? 'selected' : '' ?>>
                                            <?= $w['order_code'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
    
                            <div class="form-group">
                                <label>Checkpoint Type</label>
                                <select name="checkpoint_type" class="form-control" id="typeSelect" required>
                                    <option>-- Select --</option>
                                    <option value="Incoming" <?= $q['checkpoint_type'] == 'Incoming' ? 'selected' : '' ?>>Incoming</option>
                                    <option value="In-Process" <?= $q['checkpoint_type'] == 'In-Process' ? 'selected' : '' ?>>In-Process</option>
                                    <option value="Final" <?= $q['checkpoint_type'] == 'Final' ? 'selected' : '' ?>>Final</option>
                                </select>
                            </div>
    
                            <div class="form-group" id="materialGroup" style="<?= $q['checkpoint_type'] == 'Incoming' ? '' : 'display:none;' ?>">
                                <label>Material (Incoming only)</label>
                                <select name="material_id" class="form-control">
                                    <option value="">Select</option>
                                    <?php foreach($materials as $m): ?>
                                        <option value="<?= $m['id'] ?>" <?= $m['id'] == $q['material_id'] ? 'selected' : '' ?>>
                                            <?= $m['name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
    
                            <div class="form-group">
                                <label>Description / Process</label>
                                <input type="text" name="description" class="form-control" value="<?= $q['description'] ?>" required>
                            </div>
    
                            <div class="form-group">
                                <label>Result</label>
                                <select name="result" class="form-control" required>
                                    <option value="Pass" <?= $q['result'] == 'Pass' ? 'selected' : '' ?>>Pass</option>
                                    <option value="Fail" <?= $q['result'] == 'Fail' ? 'selected' : '' ?>>Fail</option>
                                </select>
                            </div>
    
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea name="remarks" class="form-control"><?= $q['remarks'] ?></textarea>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="form-group float-end">
                                <a href="./" class="btn btn-default">Cancel</a>
                                <button type="submit" name="action" value="update" class="btn btn-success">Update</button>
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
      <?php include("../../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../../includes/scripts.phtml"); ?>
    <script>
        const typeSelect = document.querySelector('#typeSelect');
        const materialGroup = document.querySelector('#materialGroup');

        materialGroup.style.display = typeSelect.value === 'Incoming' ? 'block' : 'none';
        
        typeSelect.addEventListener('change', function () {
            materialGroup.style.display = this.value === 'Incoming' ? 'block' : 'none';
        });
    </script>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
