<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');
include("../../functions/role_functions.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
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

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $code = $_POST['employee_code'];
  $fname = $_POST['first_name'];
  $lname = $_POST['last_name'];
  $nin = $_POST['nin_number'];
  $job = $_POST['job_title'];
  $country = $_POST['country'];
  $email = $_POST['email'];
  $department = $_POST['department'];
  $salary = $_POST['salary'];
  $company_id = $_SESSION['company_id'];
  $phone = $_POST['phone'];

  $check_email = $conn->query("SELECT id FROM employees WHERE email = '$email' AND company_id = $company_id");

  if($check_email->num_rows > 0) {
    $_SESSION['error'] = "Email Address already exist";
  } else {
    $stmt = $conn->prepare("INSERT INTO employees (employee_code, company_id, first_name, last_name, phone, nin, position, salary, country, email, department)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisssssdsss", $code, $company_id, $fname, $lname, $phone, $nin, $job, $salary, $country, $email, $department);
  
    if ($stmt->execute()) {
        $_SESSION['success'] = "Employees added successfully";
        header("Location: list_employees.php");
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
    }
  }


}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Payroll - Add Employee</title>
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
        <div class="content-header mt-3 mb-3">
          <h1>Add New Employee</h1>
        </div>
        <div class="container-fluid">
            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($message): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                  <?php echo htmlspecialchars($message); ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row">
              <div class="col-md-4">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">
                      Employee Details
                    </h3>
                  </div>
                  <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label>Employee Code</label>
                            <input type="text" name="employee_code" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                          <label for="email">E-mail Address</label>
                          <input type="text" name="email" id="" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>NIN Number</label>
                            <input type="text" name="nin_number" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Job Title</label>
                            <input type="text" name="job_title" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Department</label>
                            <input type="text" name="department" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Salary</label>
                            <input type="text" name="salary" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" name="country" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="form-group mt-3 float-end">
                          <?= (isset($_SESSION['success'])) ? '<a href="list_employees.php" class="btn btn-info">Close</a>' : '<a href="list_employees.php" class="btn btn-danger">Cancel</a>'; ?>
                          <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                  </div>
                </div>
              </div>
              <div class="col-md-8"></div>
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
