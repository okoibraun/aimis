<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');
include("../../functions/role_functions.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// $can_access = ['admin', 'superadmin', 'system'];
// if(!in_array($_SESSION['user_role'], $can_access)) {
//     die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
//     exit;
// }

// Check User Permissions
$page = "list";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$company_id = $_SESSION['company_id'];
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Payroll - List Employees</title>
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
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            </section>

            <section class="content">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Employees</h3>
                        <div class="card-tools">
                            <a href="add_employee.php" class="btn btn-primary">Add Employee</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped DataTable">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Job Title</th>
                                    <th>Salary</th>
                                    <th>Country</th>
                                    <th>NIN</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $res = $conn->query("SELECT * FROM employees WHERE company_id = $company_id ORDER BY created_at DESC");
                                foreach ($res as $row):
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['employee_code']) ?></td>
                                    <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                    <td><?= htmlspecialchars($row['position']) ?></td>
                                    <td>N<?= number_format(htmlspecialchars($row['salary']), 2) ?></td>
                                    <td><?= htmlspecialchars($row['country']) ?></td>
                                    <td><?= htmlspecialchars($row['nin']) ?></td>
                                    <td>
                                        <a href="edit_employee.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Edit</a>
                                        <a href="view_employee.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-secondary">View</a>
                                        <?php if($row['status'] == "hold") { ?> 
                                            <a href="unhold_payment.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success">Unhold Payment</a>
                                        <?php } else { ?>
                                            <a href="hold_payment.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger">Hold Payment</a>
                                        <?php } ?>
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
