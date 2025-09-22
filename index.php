<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('./config/db.php');
include("./functions/role_functions.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: login');
    exit();
}


// Memo count
$memo_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM memos WHERE company_id = $company_id"))['total'];

// User count
$user_count = $conn->query("SELECT COUNT(*) as total FROM users WHERE company_id = $company_id")->fetch_assoc()['total'];
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Dashboard</title>
    <?php include_once("includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Dashboard</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
              </div>
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-12 col-sm-6 col-md-2">
                <div class="info-box mb-3">
                  <span class="info-box-icon bg-primary elevation-1 text-white">
                    <i class="fas fa-envelope"></i>
                  </span>
                  <div class="info-box-content">
                      <span class="info-box-text">Memos</span>
                      <span class="info-box-number"><?php echo $memo_count; ?></span>
                  </div>
                  <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
              </div>
              <!-- /.col -->
              <?php if(in_array($_SESSION['user_role'], super_roles()) || in_array($_SESSION['user_role'], ['accounts'])) { ?>
              <div class="col-12 col-sm-6 col-md-2">
                <div class="info-box mb-3">
                  <span class="info-box-icon bg-success elevation-1 text-white">
                    <i class="fas fa-file-invoice-dollar"></i>
                  </span>
                  <div class="info-box-content">
                      <span class="info-box-text">Accounts</span>
                      <span class="info-box-number"><?= $conn->query("SELECT COUNT(*) AS total FROM sales_invoices WHERE company_id = $company_id")->fetch_assoc()['total']; ?></span>
                  </div>
                  <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
              </div>
              <!-- /.col -->
              <?php } ?>
              <!--end::Col-->
              <?php if(in_array($_SESSION['user_role'], super_roles()) || in_array($_SESSION['user_role'], ['hr'])) { ?>
              <div class="col-12 col-sm-6 col-md-2">
                <div class="info-box mb-3">
                  <span class="info-box-icon bg-secondary elevation-1 text-white"><i class="fas fa-users"></i></span>

                  <div class="info-box-content">
                      <span class="info-box-text">Employees</span>
                      <span class="info-box-number"><?= $conn->query("SELECT COUNT(*) AS total FROM employees WHERE company_id = $company_id")->fetch_assoc()['total'] ?></span>
                  </div>
                  <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
              </div>
              <!-- /.col -->
              <?php } ?>
              <!--end::Col-->
              <?php if(in_array($_SESSION['user_role'], super_roles()) || in_array($_SESSION['user_role'], ['sales'])) { ?>
              <div class="col-12 col-sm-6 col-md-2">
                <div class="info-box mb-3">
                  <span class="info-box-icon bg-warning elevation-1 text-white"><i class="fas fa-shopping-cart"></i></span>

                  <div class="info-box-content">
                      <span class="info-box-text">Sales</span>
                      <span class="info-box-number"><?= $conn->query("SELECT COUNT(*) AS total FROM sales_invoices WHERE company_id = $company_id")->fetch_assoc()['total']; ?></span>
                  </div>
                  <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
              </div>
              <!-- /.col -->
              <!--end::Col-->
              <div class="col-12 col-sm-6 col-md-2">
                <div class="info-box mb-3">
                  <span class="info-box-icon bg-info elevation-1 text-white"><i class="fas fa-headset"></i></span>

                  <div class="info-box-content">
                      <span class="info-box-text">CRM</span>
                      <span class="info-box-number"><?= $conn->query("SELECT COUNT(*) AS total FROM sales_orders WHERE company_id = $company_id")->fetch_assoc()['total']; ?></span>
                  </div>
                  <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
              </div>
              <!-- /.col -->
              <div class="col-12 col-sm-6 col-md-2">
                <div class="info-box mb-3">
                  <span class="info-box-icon bg-danger elevation-1 text-white"><i class="fas fa-cogs"></i></span>

                  <div class="info-box-content">
                      <span class="info-box-text">Productions</span>
                      <span class="info-box-number"><?= $conn->query("SELECT COUNT(*) AS total FROM production_work_orders WHERE company_id = $company_id")->fetch_assoc()['total']; ?></span>
                  </div>
                  <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
              </div>
              <!-- /.col -->
            </div>
            <!--end::Row-->
            <div class="row">
              <div class="col-6">
                <?php $today = date('Y-m-d H:i', strtotime(date('Y-m-d H:i'))); ?>
                <?php $reminders = $conn->query("SELECT * FROM crm_reminders WHERE company_id = $company_id AND user_id = $user_id AND is_done = 0 ORDER BY due_at DESC"); ?>
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">
                      Reminders
                    </h3>
                  </div>
                  <div class="card-body table-responsive">
                      <table class="table table-bordered">
                          <thead>
                              <tr>
                              <th>Due Date</th>
                              <th>Reminder</th>
                              <th>Linked To</th>
                              <th>Status</th>
                              <th>Actions</th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php foreach ($reminders as $reminder): ?>
                              <tr class="<?= $reminder['is_done'] ? 'text-muted' : '' ?>">
                                  <td><?= date('Y-m-d H:i', strtotime($reminder['due_at'])) ?></td>
                                  <td><?= htmlspecialchars($reminder['reminder_text']) ?></td>
                                    <?php $related_to = $conn->query("SELECT name, title, customer_type FROM sales_customers WHERE id={$reminder['related_id']}")->fetch_assoc(); ?>
                                    <td><?= ucfirst($reminder['related_type']) ?> : <?= !empty($reminder['related_id']) ? ($related_to['customer_type'] == 'customer' ? $related_to['name'] : $related_to['title']) : 'none'; ?></td>
                                  <td>
                                    <?= $reminder['is_done'] ? '<span class="text text-success">Done</span>' : '<span class="text text-warning">Pending</span>' ?>
                                  </td>
                                  <td>
                                    <?php if (!$reminder['is_done']): ?>
                                      <a href="/modules/crm/reminders/complete.php?id=<?= $reminder['id'] ?>&reflink=home" class="btn btn-xs btn-success">Mark Done</a>
                                    <?php endif; ?>
                                  </td>
                              </tr>
                              <?php endforeach; ?>
                          </tbody>
                      </table>
                  </div>
                </div>
              </div>
            </div>
            <?php } ?>
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("./includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("./includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
