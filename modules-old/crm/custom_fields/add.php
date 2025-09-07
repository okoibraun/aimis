<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

$module = $_GET['module'] ?? 'contact';
$allowed_modules = ['contact', 'company', 'deal'];
if (!in_array($module, $allowed_modules)) {
    $module = 'contact';
}

$page_title = "Add Custom Field";
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | CRM - Custom Fields</title>
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
          

          <!-- Main Content -->
          <div class="content-wrapper">
            <section class="content-header">
                <h1>Add Custom Field <small>for <?= ucfirst($module) ?></small></h1>
            </section>

            <section class="content">
                <div class="box box-primary">
                <form action="save.php" method="POST">
                    <input type="hidden" name="module" value="<?= $module ?>">

                    <div class="box-body">
                    <div class="form-group">
                        <label for="name">Field Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="field_type">Field Type</label>
                        <select name="field_type" class="form-control" id="field_type" required>
                        <option value="text">Text</option>
                        <option value="number">Number</option>
                        <option value="date">Date</option>
                        <option value="select">Select (Dropdown)</option>
                        </select>
                    </div>

                    <div class="form-group" id="options_group" style="display: none;">
                        <label for="options">Options (for Select, comma-separated)</label>
                        <textarea name="options" class="form-control" rows="3" placeholder="Option1,Option2,..."></textarea>
                    </div>
                    </div>

                    <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Save Field</button>
                    <a href="index.php?module=<?= $module ?>" class="btn btn-default">Cancel</a>
                    </div>
                </form>
                </div>
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
    <script>
    document.getElementById('field_type').addEventListener('change', function () {
    document.getElementById('options_group').style.display =
        this.value === 'select' ? 'block' : 'none';
    });
    </script>
  </body>
  <!--end::Body-->
</html>
