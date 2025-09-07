<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');
include("../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Check User Permissions
$page = "add";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$reconciliation_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bank_statement_id = $_POST['bank_statement_id'];
    $reconciliation_date = $_POST['reconciliation_date'];
    $status = $_POST['status'];
    
    // Insert the reconciliation into the database
    $stmt = mysqli_prepare($conn, "
        INSERT INTO bank_reconciliations (bank_statement_id, reconciliation_date, status)
        VALUES (?, ?, ?)
    ");
    mysqli_stmt_bind_param($stmt, 'iss', $bank_statement_id, $reconciliation_date, $status);
    $reconciliation_success = mysqli_stmt_execute($stmt);
    
    // Update the bank statement to mark it as reconciled
    if ($reconciliation_success) {
        $update_stmt = mysqli_prepare($conn, "
            UPDATE bank_statements SET reconciled = 1 WHERE id = ?
        ");
        mysqli_stmt_bind_param($update_stmt, 'i', $bank_statement_id);
        mysqli_stmt_execute($update_stmt);
    }
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Accounts - Reconcile Bank Statement</title>
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

              <div class="row mt-4 mb-4">
                  <div class="col-lg-12">
                      <div class="float-end">
                          <!-- <a href="list_schedules.php" class="btn btn-primary">List Schedules</a> -->
                      </div>
                  </div>
              </div>

              <div class="content-wrapper">
                  <section class="content-header">
                    <h1>Reconcile Bank Statement</h1>
                  </section>

                  <section class="content">
                    <?php if ($reconciliation_success): ?>
                      <div class="alert alert-success">Bank statement reconciled successfully.</div>
                    <?php endif; ?>

                    <!-- Reconciliation Form -->
                    <div class="card">
                      <div class="card-header">
                        <h3 class="card-title">Reconciliation Details</h3>
                      </div>
                      <div class="card-body">
                        <form method="POST">
                          <div class="form-group">
                            <label>Bank Statement</label>
                            <select name="bank_statement_id" class="form-control" required>
                              <?php
                                // Fetch bank statements
                                $stmt = mysqli_query($conn, "SELECT id, statement_number FROM bank_statements WHERE reconciled = 0");
                                foreach ($stmt as $row) {
                              ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['statement_number']; ?></option>
                              <?php } ?>
                            </select>
                          </div>

                          <div class="form-group">
                            <label>Reconciliation Date</label>
                            <input type="date" name="reconciliation_date" class="form-control" required>
                          </div>

                          <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                              <option value="Completed">Completed</option>
                              <option value="Pending">Pending</option>
                            </select>
                          </div>

                          <button type="submit" class="btn btn-primary">Reconcile Statement</button>
                        </form>
                      </div>
                    </div>

                  </section>
              </div>
              

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