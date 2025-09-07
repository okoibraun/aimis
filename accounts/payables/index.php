<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');
include("../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

// Check User Permissions
$page = "list";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

// Get all bills
// $whereClause = '';
// if (isset($_GET['search']) && !empty($_GET['search'])) {
//     $search = mysqli_real_escape_string($conn, $_GET['search']);
//     $whereClause = "WHERE vendor LIKE '%$search%' OR reference LIKE '%$search%'";
// }
// $bills_result = mysqli_query($conn, "
//     SELECT * FROM bills
//     $whereClause
//     ORDER BY due_date DESC
// ");

if(in_array($_SESSION['user_role'], system_users())) {
  $bills_result = $conn->query("SELECT b.*, v.name AS vendor_name FROM bills b JOIN accounts_vendors v ON b.vendor_id = v.id ORDER BY due_date DESC");
} else {
  $bills_result = $conn->query("SELECT b.*, v.name AS vendor_name FROM bills b JOIN accounts_vendors v ON b.vendor_id = v.id WHERE b.company_id = $company_id ORDER BY due_date DESC");
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Bills</title>
    <?php include_once("../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">

          <section class="content-header mt-3 mb-5">
            <h1>Vendor Bills</h1>
          </section>

          <section class="content">
            <!-- Search Bar -->
            <!-- <div class="card">
              <div class="card-body">
                <form class="form-inline" method="GET">
                  <div class="form-group mr-2">
                    <input type="text" name="search" class="form-control" placeholder="Search by Vendor/Reference" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
                  </div>
                  <button type="submit" class="btn btn-primary">Search</button>
                </form>
              </div>
            </div> -->

            <!-- <div class="row mt-4 mb-4">
              <div class="col-lg-12">
                <div class="float-end">
                  <a href="add" class="btn btn-primary">Add Bill</a>
                </div>
              </div>
            </div> -->

            <!-- Bill Table -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">All Bills</h3>
                <div class="card-tools">
                  <a href="add" class="btn btn-primary">Create New Bill</a>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Bill #</th>
                        <th>Vendor</th>
                        <th>Bill Date</th>
                        <th>Due Date</th>
                        <th>Total Amount</th>
                        <th>Balance</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php while ($bill = mysqli_fetch_assoc($bills_result)): ?>
                        <tr>
                          <td><?= $bill['id'] ?></td>
                          <td><?= htmlspecialchars($bill['vendor_name']) ?></td>
                          <td><?= $bill['bill_date'] ?></td>
                          <td><?= $bill['due_date'] ?></td>
                          <td><?= number_format($bill['amount'], 2) ?></td>
                          <td><?= number_format($bill['amount'] - $bill['paid_amount'], 2) ?></td>
                          <td>
                            <a href="bill?id=<?= $bill['id'] ?>" class="btn btn-info btn-sm">View</a>
                            <a href="payments?bill_id=<?= $bill['id'] ?>" class="btn btn-success btn-sm">Pay</a>
                          </td>
                        </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

          </section>

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
