<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');
include("../../../functions/role_functions.php");

if (!isset($user_id)) {
    header('Location: /login.php');
    exit();
}

// Check User Permissions
$page = "assign";
$user_permissions = get_user_permissions($user_id);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$work_order_id = $_GET['work_order_id'] ?? 1;
$resources = $conn->query("SELECT * FROM production_resources WHERE company_id = $company_id ORDER BY type, name");
$assigned = $conn->query("SELECT * FROM production_work_order_resources WHERE company_id = $company_id AND work_order_id = $work_order_id");
$assigned_map = [];
foreach ($assigned as $row) {
    $assigned_map[$row['resource_id']] = $row['assigned_hours'];
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Production - Assign Resources</title>
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
                    <h1>Assign Resources to Work Order </h1>
                </section>
                <section class="content">
                    <form action="save.php" method="post" class="card">
                        <div class="card-body">
                            <input type="hidden" name="work_order_id" value="<?= $work_order_id ?>">
                            <table class="table table-bordered">
                                <thead><tr><th>Resource</th><th>Assigned Hours</th></tr></thead>
                                <tbody>
                                    <?php while ($r = mysqli_fetch_assoc($resources)): ?>
                                        <tr>
                                            <td>
                                                <label>
                                                    <input type="checkbox" name="resource_id[]" value="<?= $r['id'] ?>"
                                                        <?= isset($assigned_map[$r['id']]) ? 'checked' : '' ?>>
                                                    <?= $r['name'] ?> (<?= $r['type'] ?>)
                                                </label>
                                            </td>
                                            <td>
                                                <input type="number" step="0.1" name="assigned_hours[<?= $r['id'] ?>]"
                                                    class="form-control"
                                                    value="<?= $assigned_map[$r['id']] ?? '' ?>">
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer">
                            <div class="form-group float-end">
                                <a href="./" class="btn btn-default">Cancel</a>
                                <button type="submit" name="action" value="assign" class="btn btn-success">Assign Resources</button>
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
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
