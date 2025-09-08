<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');
include("../../functions/role_functions.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>No employee ID provided.</div>";
    exit;
}

// Check User Permissions
$page = "view";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$company_id = $_SESSION['company_id'];
$employee_id = intval($_GET['id']);

// Fetch employee details
$stmt = $conn->query("SELECT * FROM employees WHERE company_id = $company_id AND id=$employee_id");
if ($stmt->num_rows === 0) {
    $_SESSION['error'] = "Employee Not Found";
    header("Location: list_employees.php");
}
$emp = $stmt->fetch_assoc();
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Payroll - View Employee</title>
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

            <div class="content-wrapper">
                <section class="content-header mt-3 mb-5">
                    <?php if(isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                </section>

                <section class="content">
                    <div class="row">

                    <!-- Profile Card -->
                    <div class="col-md-8">
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">
                                <h3 class="profile-username text-center">
                                    <?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?>
                                    <div class="card-tools mt-3 mb-3 float-end">
                                        <a href="list_employees.php" class="btn btn-primary"> Back </a>
                                        <a href="edit_employee.php?id=<?= $emp['id']; ?>" class="btn btn-primary" title="Edit Employee">
                                            <i class="bi bi-pencil bi bi-edit"></i>
                                        </a>
                                    </div>
                                </h3>
                                <p class="text-muted text-center"><?= $emp['position'] ?> | <?= $emp['department'] ?></p>
                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item">
                                        <b>Email: </b> <span class="float-right"><?= $emp['email'] ?></span>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Phone: </b> <span class="float-right"><?= $emp['phone'] ?></span>
                                    </li>
                                    <li class="list-group-item">
                                        <b>NIN/SSN: </b> <span class="float-right"><?= $emp['nin'] ?></span>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Country: </b> <span class="float-right"><?= $emp['country'] ?></span>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Salary: </b> <span class="float-right">N<?= number_format($emp['salary'], 2) ?></span>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Bank Account: </b> <span class="float-right"><?= $emp['bank_account'] ?></span>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Status: </b> <span class="float-right"><?= ucfirst($emp['status']) ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <?php $bonuses = $conn->query("SELECT * FROM employee_bonuses WHERE company_id = $company_id AND employee_id = $employee_id"); ?>
                        <div class="card mt-4 mb-4">
                            <div class="card-header">Employee Bonuses</div>
                            <div class="card-body">
                                <table class="table table-stripped">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th>Amount</th>
                                            <th>Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?= ($bonuses->num_rows == 0) ? 'No records found' : ''; ?>
                                        <?php foreach($bonuses as $bonus) { ?>
                                        <tr>
                                            <td><?= $bonus['month']; ?></td>
                                            <td><?= $bonus['amount']; ?></td>
                                            <td><?= $bonus['reason']; ?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <?php $bank_accounts = $conn->query("SELECT * FROM bank_accounts WHERE company_id = $company_id AND employee_id = $employee_id"); ?>
                        <div class="card mt-4 mb-4">
                            <div class="card-header">Employee Bank Accounts</div>
                            <div class="card-body">
                                <table class="table table-stripped">
                                    <thead>
                                        <tr>
                                            <th>Bank</th>
                                            <th>Account Name</th>
                                            <th>Account Number</th>
                                            <th>Currency</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?= ($bank_accounts->num_rows == 0) ? 'No records found' : ''; ?>
                                        <?php foreach($bank_accounts as $account) { ?>
                                        <tr>
                                            <td><?= $account['bank_name']; ?></td>
                                            <td><?= $account['account_name']; ?></td>
                                            <td><?= $account['account_number']; ?></td>
                                            <td><?= $account['currency']; ?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Linked Modules -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Payroll & HR Modules</h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="../payslips/view_payslips.php?employee_id=<?= $employee_id ?>">View Payslips</a>
                                        <span class="badge badge-info">Payslip History</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="../attendance/view_attendance.php?employee_id=<?= $employee_id ?>">Attendance Records</a>
                                        <span class="badge badge-secondary">Timesheets</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="../leave/view_leave.php?employee_id=<?= $employee_id ?>">Leave Applications</a>
                                        <span class="badge badge-warning">Leave History</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="../contracts/view_contract.php?employee_id=<?= $employee_id ?>">Contract Details</a>
                                        <span class="badge badge-primary">Contract</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="../bank/bank_payment.php?employee_id=<?= $employee_id ?>">Bank Payment Info</a>
                                        <span class="badge badge-success">Payments</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="../tax/view_tax.php?employee_id=<?= $employee_id ?>">Tax & Compliance</a>
                                        <span class="badge badge-danger">Tax</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
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

<?php include '../../includes/footer.php'; ?>