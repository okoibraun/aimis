<?php
require_once '../../includes/helpers.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../login.php');
    exit();
}

$is_edit = isset($_GET['id']);
$lead = ['customer_id'=>'','title'=>'','description'=>'','lead_date'=>'','status'=>'New','assigned_to'=>''];
$customers = get_all_rows('sales_customers');

if ($is_edit) {
    $lead = get_row_by_id('sales_leads', $_GET['id']);
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
                <h1>
                    <?= $is_edit ? 'Edit' : 'Add' ?> Lead
                </h1>
            </section>

            <section class="row content">
                <div class="col-lg-8">
                    <div class="card">
                        <form method="POST" action="../../controllers/leads.php" class="card-content">
                            <input type="hidden" name="action" value="add">
                            <div class="card-header">
                                <h5 class="card-title">New Lead Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Customer ID</label>
                                    <input name="customer_id" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Title</label>
                                    <input name="title" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Date</label>
                                    <input type="date" name="lead_date" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <?php foreach(['New', 'Contacted', 'Qualified', 'Lost', 'Won', 'Closed', 'Converted'] as $status): ?>
                                        <option><?= $status; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Source</label>
                                    <select name="source" class="form-control">
                                        <?php foreach(['Web', 'Email', 'Phone', 'Referral', 'Event', 'Other'] as $source): ?>
                                        <option><?= $source; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-success float-end">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
            

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
