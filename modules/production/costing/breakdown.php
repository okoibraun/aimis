<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

$wo_id = $_GET['work_order_id'];
$wo = $conn->query("SELECT * FROM production_work_orders WHERE id = $wo_id")->fetch_assoc();
$items = $conn->query("SELECT * FROM production_cost_breakdown WHERE work_order_id = $wo_id");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Production - Costing</title>
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
                <section class="content-header">
                    <h1>Cost Breakdown - <?= $wo['order_code'] ?></h1>
                </section>
                <section class="content">
                    <form action="save.php" method="post">
                        <input type="hidden" name="work_order_id" value="<?= $wo_id ?>">
                        <table class="table table-bordered" id="cost-table">
                            <thead>
                                <tr><th>Type</th><th>Description</th><th>Estimated</th><th>Actual</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                                <?php while($i = mysqli_fetch_assoc($items)): ?>
                                <tr>
                                    <td>
                                        <select name="type[]" class="form-control">
                                            <option value="Material" <?= $i['type']=='Material'?'selected':'' ?>>Material</option>
                                            <option value="Labor" <?= $i['type']=='Labor'?'selected':'' ?>>Labor</option>
                                            <option value="Machine" <?= $i['type']=='Machine'?'selected':'' ?>>Machine</option>
                                            <option value="Overhead" <?= $i['type']=='Overhead'?'selected':'' ?>>Overhead</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="description[]" class="form-control" value="<?= $i['description'] ?>"></td>
                                    <td><input type="number" step="0.01" name="estimated[]" class="form-control" value="<?= $i['estimated'] ?>"></td>
                                    <td><input type="number" step="0.01" name="actual[]" class="form-control" value="<?= $i['actual'] ?>"></td>
                                    <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">Remove</button></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>

                        <button type="button" class="btn btn-secondary" onclick="addRow()">+ Add Line</button>
                        <button type="submit" class="btn btn-success">Save Costing</button>
                    </form>

                    <script>
                    function addRow() {
                        const row = `<tr>
                            <td>
                                <select name="type[]" class="form-control">
                                    <option value="Material">Material</option>
                                    <option value="Labor">Labor</option>
                                    <option value="Machine">Machine</option>
                                    <option value="Overhead">Overhead</option>
                                </select>
                            </td>
                            <td><input type="text" name="description[]" class="form-control"></td>
                            <td><input type="number" step="0.01" name="estimated[]" class="form-control"></td>
                            <td><input type="number" step="0.01" name="actual[]" class="form-control"></td>
                            <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">Remove</button></td>
                        </tr>`;
                        document.querySelector('#cost-table tbody').insertAdjacentHTML('beforeend', row);
                    }
                    </script>
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
