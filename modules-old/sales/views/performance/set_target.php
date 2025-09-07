<?php
require_once '../../includes/helpers.php'; // Include your helper functions

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $month = date('Y-m', strtotime($month)); // Ensure the month is in Y-m format
    $amount = $_POST['target_amount'];
    
    $db->query("INSERT INTO sales_targets (user_id, target_month, target_amount) VALUES ($user_id, NOW(), $amount)");
    
    redirect('../../controllers/performance.php');
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Sales - Targets</title>
    <?php include_once("../../../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">
          

          <div class="content-wrapper">
            <section class="content-header">
                <h1>Set Sales Target</h1>
            </section>
            <section class="content">
                <form method="post" class="box box-primary p-3">
                    <div class="form-group">
                    <label for="user_id">Sales Person</label>
                    <select name="user_id" class="form-control">
                        <?php foreach ($db->query("SELECT id, name FROM users") as $user): ?>
                        <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    </div>
                    <div class="form-group">
                      <label for="target_month">Target Month</label>
                      <input type="date" name="target_month" class="form-control" required />
                    </div>
                    <div class="form-group">
                      <label for="target_amount">Target Amount</label>
                      <input type="number" name="target_amount" class="form-control" required />
                    </div>
                    <button class="btn btn-primary">Save</button>
                    <a href="../../controllers/performance.php" class="btn btn-default">Cancel</a>
                </form>
            </section>

          </div>
          

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
