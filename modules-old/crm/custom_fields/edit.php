<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

$id = (int) ($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM crm_custom_field_definitions WHERE id = ? AND company_id = ?");
$stmt->bind_param('ii', $id, $_SESSION['company_id']);
$stmt->execute();
$result = $stmt->get_result();
$field = $result->fetch_assoc();

if (!$field) {
    die('Custom field not found.');
}

$page_title = "Edit Custom Field";
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
                <h1>Edit Custom Field <small><?= ucfirst($field['module']) ?></small></h1>
            </section>

            <section class="content">
                <div class="box box-primary">
                <form action="update.php" method="POST">
                    <input type="hidden" name="id" value="<?= $field['id'] ?>">

                    <div class="box-body">
                    <div class="form-group">
                        <label for="name">Field Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($field['name']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Field Type</label>
                        <input type="text" class="form-control" value="<?= $field['field_type'] ?>" disabled>
                    </div>

                    <?php if ($field['field_type'] === 'select'): ?>
                    <div class="form-group" id="options_group">
                        <label for="options">Options (comma-separated)</label>
                        <textarea name="options" class="form-control" rows="3"><?= htmlspecialchars($field['options']) ?></textarea>
                    </div>
                    <?php endif; ?>
                    </div>

                    <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="index.php?module=<?= $field['module'] ?>" class="btn btn-default">Cancel</a>
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
