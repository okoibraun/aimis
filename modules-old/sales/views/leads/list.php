<?php
require_once '../../includes/helpers.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../login.php');
    exit();
}

$leads = mysqli_query($conn, "SELECT l.*, c.company_id FROM sales_leads l LEFT JOIN sales_customers c ON l.customer_id = c.id");
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
            
            <!-- <section class="content-header">
              <h1>Sales Leads</h1>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../../index.php">Home</a></li>
                <li class="breadcrumb-item active">Leads</li>
              </ol>
            </section> -->

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
                      <a href="add_lead.php" class="btn btn-primary">Add Lead</a>
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
                        <td><?= htmlspecialchars($lead['company_id']) ?></td>
                        <td><?= $lead['lead_date'] ?></td>
                        <td><?= $lead['status'] ?></td>
                        <td><?= $lead['assigned_to'] ?></td>
                        <td>
                          <a href="edit.php?id=<?= $lead['id'] ?>" class="btn btn-sm btn-info">Edit</a>
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
