<?php
require_once '../../includes/helpers.php';
include("../../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

// Check User Permissions
$page = "list";
$user_permissions = get_user_permissions($_SESSION['user_id']);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

if(in_array($_SESSION['user_role'], system_users())) {
  $leads = $conn->query("SELECT l.*, c.name AS customer_name FROM sales_leads l JOIN sales_customers c ON l.customer_id = c.id");
} else {
  $leads = $conn->query("SELECT l.*, c.name AS customer_name FROM sales_leads l JOIN sales_customers c ON l.customer_id = c.id WHERE l.company_id = $company_id");
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Sales - Leads</title>
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
            
            <section class="content-header">
              <h1>Sales Leads</h1>
              <ol class="breadcrumb float-end">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active">Leads</li>
              </ol>
            </section>

            <section class="content mt-5">
              <div class="card">
                <div class="card-header">
                  <div class="row">
                    <div class="col-md-6">
                      <h4>Leads</h4>
                    </div>
                    <div class="col-md-6 text-end">
                      <!-- <button class="btn btn-primary" data-toggle="modal" data-target="#leadModal">Add Lead</button> -->
                      <!-- <a href="add_lead.php" class="btn btn-primary float-end">Add Lead</a> -->
                      <a href="add" class="btn btn-primary">Add Lead</a>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Title</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php foreach($leads as $lead): ?>
                      <tr>
                        <td><?= htmlspecialchars($lead['title']) ?></td>
                        <td><?= htmlspecialchars($lead['customer_name']) ?></td>
                        <td><?= $lead['lead_date'] ?></td>
                        <td><?= $lead['status'] ?></td>
                        <td><?= ($lead['assigned_to'] != 0) ? $conn->query("SELECT name FROM users WHERE id ={$lead['assigned_to']}")->fetch_assoc()['name'] : ''; ?></td>
                        <td>
                          <a href="edit?id=<?= $lead['id'] ?>" class="btn btn-sm btn-info">Edit</a>
                          <form method="POST" action="../../controllers/leads.php" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $lead['id'] ?>">
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this lead?')">Delete</button>
                          </form>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </section>

            <?php include("_modal.php"); ?>
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
