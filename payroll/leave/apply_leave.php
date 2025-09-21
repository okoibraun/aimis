<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Mark Attendance</title>
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
            <section class="content-wrapper">
                <section class="content-header mt-3 mb-3">
                    <h3>Apply for Leave</h3>
                </section>
                <section class="content">
                    <div class="card">
                        <div class="card-body">
                            <form action="leave_requests.php" method="POST">
                                <div class="row">
                                    <?php if(!isset($_GET['employee_id'])) { ?>
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Employee</label>
                                            <select name="employee_id" class="form-control" required>
                                                <option value="">-- Select --</option>
                                                <?php
                                                $res = $conn->query("SELECT id, first_name, last_name FROM employees WHERE company_id = $company_id AND status='active'");
                                                foreach ($res as $row):
                                                ?>
                                                    <option value="<?= $row['id'] ?>"><?= $row['first_name'] . ' ' . $row['last_name'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php } else { ?>
                                        <input type="hidden" name="employee_id" value="<?= $_GET['employee_id'] ?? $_SESSION['employee_id'] ?>">
                                    <?php } ?>
                                    <div class="col-auto">
                                        <div class="form-group">
                                            <label>Leave Type</label>
                                            <select name="leave_type" class="form-control" required>
                                                <option value="sick">Sick Leave</option>
                                                <option value="annual">Annual Leave</option>
                                                <option value="maternity">Maternity Leave</option>
                                                <option value="paternity">Paternity Leave</option>
                                                <option value="unpaid">Unpaid Leave</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="form-group">
                                            <label>Start Date</label>
                                            <input type="date" name="start_date" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="form-group">
                                            <label>End Date</label>
                                            <input type="date" name="end_date" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                
                                <div class="form-group">
                                    <label>Reason</label>
                                    <textarea name="reason" id="summernote" class="form-control" rows="3" placeholder="Explain your reason..." required></textarea>
                                </div>
                
                                <?= isset($_GET['employee_id']) ? '<a href="view_leave.php" class="btn btn-secondary">Cancel</a>' : '<a href="leave_requests.php" class="btn btn-secondary">Cancel</a>'; ?>
                                <button type="submit" name="submit_leave" class="btn btn-primary">Submit Leave Request</button>
                            </form>
                        </div>
                    </div>
                </section>
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
