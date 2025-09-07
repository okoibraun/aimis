<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

$module = $_GET['module'] ?? '';
$allowed_modules = ['contact', 'company', 'deal'];
if (!in_array($module, $allowed_modules)) {
    $module = 'contact';
}

$page_title = "Add Tag";
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | CRM - Tags</title>
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
                <h1>Add Tag <small><?= ucfirst($module) ?></small></h1>
            </section>

            <section class="content">
                <div class="box box-primary">
                <form action="save.php" method="POST">
                    <input type="hidden" name="module" value="<?= $module ?>">

                    <div class="box-body">
                    <div class="form-group">
                        <label for="name">Tag Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="color">Color (hex)</label>
                        <input type="color" name="color" class="form-control" value="#0073b7">
                    </div>
                    </div>

                    <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
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
  </body>
  <!--end::Body-->
</html>
