<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';
include("../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

// Check User Permissions
$page = "list";
$user_permissions = get_user_permissions($user_id);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$reminders = $conn->query("SELECT * FROM crm_reminders WHERE company_id = $company_id AND user_id = $user_id ORDER BY due_at ASC");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | CRM - Reminders</title>
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
              <div class="row">
                <div class="col-lg-6">
                  <h2 class="">CRM Reminders</h2>
                </div>
                <div class="col-lg-6 text-end">
                  <ol class="breadcrumb float-end">
                    <li class="breadcrumb-item"><a href="/"><i class="fa fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item"><a href="#">CRM</a></li>
                    <li class="breadcrumb-item active">Reminders</li>
                  </ol>
                </div>
              </div>
            </section>

            <section class="content">
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
                                    <a href="complete.php?id=<?= $reminder['id'] ?>" class="btn btn-xs btn-success">Mark Done</a>
                                  <?php endif; ?>
                                  <a href="delete.php?id=<?= $reminder['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete this reminder?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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
