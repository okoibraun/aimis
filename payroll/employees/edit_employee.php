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

// Check User Permissions
$page = "edit";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$emp_id = $_GET['id'];
$emp = $conn->query("SELECT * FROM employees WHERE id = $emp_id")->fetch_assoc();

$company_id = $_SESSION['company_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['employeeUpdateForm'])) {
    $id = $_POST['id'];
    $code = $_POST['employee_code'];
    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];
    $job = $_POST['job_title'];
    $country = $_POST['country'];
    $nin = $_POST['nin_number'];
    $email = $_POST['email'];
    $salary = $_POST['salary'];
    $phone = $_POST['phone'];
    $tax_deduction = $_POST['tax_compliance_id'];
    $user_id = $_POST['user_id'];

    $stmt = $conn->prepare("UPDATE employees SET user_id=?, employee_code=?, first_name=?, last_name=?, phone=?, position=?, salary=?, country=?, nin=?, email=?, tax_compliance_id=? WHERE id=? AND company_id=?");
    $stmt->bind_param("isssssdsssiii", $user_id, $code, $fname, $lname, $phone, $job, $salary, $country, $nin, $email, $tax_deduction, $id, $company_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Employee Updated Successfully.";
        header("Location: list_employees.php");
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
    }
}

// Add Bank Account
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['addAccountFormBtn'])) {
    $company_id = $_POST['company_id'];
    $employee_id = $_POST['employee_id'];
    $account_name = $_POST['account_name'];
    $account_number = $_POST['account_number'];
    $bank_name = $_POST['bank_name'];
    $currency = $_POST['currency'];

    $stmt = $conn->prepare("INSERT INTO bank_accounts (company_id, employee_id, account_name, account_number, bank_name, currency) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissss", $company_id, $employee_id, $account_name, $account_number, $bank_name, $currency);

    if($stmt->execute()) {
        $_SESSION['success'] = "Bank account added successfully.";
        header("Location: edit_employee.php?id={$emp_id}");
    } else {
        $_SESSION['error'] = "Failed to add Bank Account, try again..";
        // header("Location: edit_employee.php?id={$emp_id}");
    }
}

// Add Employee Bonuses
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['addBonusFormBtn'])) {
    $company_id = $_POST['company_id'];
    $employee_id = $_POST['employee_id'];
    $month = $_POST['month'];
    $amount = $_POST['amount'];
    $reason = $_POST['reason'];

    $stmt = $conn->prepare("INSERT INTO employee_bonuses (company_id, employee_id, month, amount, reason) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisds", $company_id, $employee_id, $month, $amount, $reason);

    if($stmt->execute()) {
        $_SESSION['success'] = "Bonus added successfully.";
        header("Location: edit_employee.php?id={$emp_id}");
    } else {
        $_SESSION['error'] = "Failed to add Bonus, try again..";
        // header("Location: edit_employee.php?id={$emp_id}");
    }
}

// Add Deduction
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['addDeductionsFormBtn'])) {
    $company_id = $_POST['company_id'];
    $employee_id = $_POST['employee_id'];
    $title = $_POST['title'];
    $month = $_POST['month'];
    $amount = $_POST['amount'];
    $notes = $_POST['notes'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO employee_deductions (company_id, employee_id, title, month, amount, notes, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissdsi", $company_id, $employee_id, $title, $month, $amount, $notes, $user_id);

    if($stmt->execute()) {
        $_SESSION['success'] = "Deduction added successfully.";
        header("Location: edit_employee.php?id={$emp_id}");
    } else {
        $_SESSION['error'] = "Failed to add Deduction, try again..";
        // header("Location: edit_employee.php?id={$emp_id}");
    }
}

// Add Allowances
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['addAllowancesFormBtn'])) {
    $company_id = $_POST['company_id'];
    $employee_id = $_POST['employee_id'];
    $title = $_POST['title'];
    $month = $_POST['month'];
    $amount = $_POST['amount'];
    $notes = $_POST['notes'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO employee_allowances (company_id, employee_id, title, month, amount, notes, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissdsi", $company_id, $employee_id, $title, $month, $amount, $notes, $user_id);

    if($stmt->execute()) {
        $_SESSION['success'] = "Allowance added successfully.";
        header("Location: edit_employee.php?id={$emp_id}");
    } else {
        $_SESSION['error'] = "Failed to add Allowance, try again..";
        // header("Location: edit_employee.php?id={$emp_id}");
    }
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Payroll - Edit Employee</title>
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
            <?php if(isset($_SESSION['error'])) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php } else if(isset($_SESSION['success'])) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php } ?>

            <div class="content-wrapper">
                <div class="content-header mt-3 mb-3">
                    <h3>Edit Employee</h3>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Employee Details</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="id" value="<?= $emp['id'] ?>">
                                <div class="row mb-2">
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Employee Code</label>
                                            <input type="text" name="employee_code" class="form-control" value="<?= $emp['employee_code'] ?>" required readonly>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="form-group">
                                            <?php $users = $conn->query("SELECT * FROM users WHERE company_id = $company_id"); ?>
                                            <label>Assign to User</label>
                                            <select name="user_id" id="" class="form-control select2">
                                                <?php foreach($users as $user) { ?>
                                                <option value="<?= $user['id']; ?>" <?= ($user['id'] == $emp['user_id']) ? 'selected' : ''; ?>><?= $user['name']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col">
                                        <div class="form-group">
                                            <label>First Name</label>
                                            <input type="text" name="first_name" class="form-control" value="<?= $emp['first_name'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Last Name</label>
                                            <input type="text" name="last_name" class="form-control" value="<?= $emp['last_name'] ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>E-mail Address</label>
                                    <input type="email" name="email" class="form-control" value="<?= $emp['email'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Job Title</label>
                                    <input type="text" name="job_title" class="form-control" value="<?= $emp['position'] ?>">
                                </div>
                                <div class="form-group">
                                    <label>Salary</label>
                                    <input type="text" name="salary" class="form-control" value="<?= $emp['salary'] ?>">
                                </div>
                                <div class="form-group">
                                    <label>Country</label>
                                    <input type="text" name="country" class="form-control" value="<?= $emp['country'] ?>">
                                </div>
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="phone" class="form-control" value="<?= $emp['phone'] ?>">
                                </div>
                                <div class="form-group">
                                    <label>NIN Number</label>
                                    <input type="text" name="nin_number" class="form-control" value="<?= $emp['nin'] ?>">
                                </div>
                                <div class="form-group">
                                    <?php $taxes = $conn->query("SELECT * FROM employee_tax_compliance WHERE company_id = $company_id"); ?>
                                    <label for="tax">Tax Deduction</label>
                                    <select name="tax_compliance_id" class="form-control select2">
                                        <?php foreach($taxes as $tax) { ?>
                                        <option value="<?= $tax['id']; ?>" <?= ($tax['id'] == $emp['tax_compliance_id']) ? 'selected' : ''; ?>><?= "{$tax['country']} - {$tax['tax_rate']}"; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group mt-3 float-end">
                                    <?= (isset($_SESSION['success'])) ? '<a href="list_employees.php" class="btn btn-info">Close</a>' : '<a href="list_employees.php" class="btn btn-danger">Cancel</a>'; ?>
                                    <button type="submit" class="btn btn-success" name="employeeUpdateForm">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-5">
                    <div class="row">
                        <div class="col-lg-12">
                            <?php
                                $bank_accounts = $conn->query("SELECT * FROM bank_accounts WHERE company_id = $company_id AND employee_id = $emp_id");
                                $num_rows = $bank_accounts->num_rows;
                            ?>
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        Bank Accounts
                                    </h3>
                                    <div class="card-tools">
                                        <!-- <div class="btn-group">
                                            <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" data-offset="-52" aria-expanded="true">
                                                <i class="fas fa-bars"></i>
                                            </button>
                                            <div class="dropdown-menu" role="menu" x-placement="top-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-52px, -132px, 0px);">
                                                <a href="#" class="dropdown-item">Add new event</a>
                                                <a href="#" class="dropdown-item">Clear events</a>
                                                <div class="dropdown-divider"></div>
                                                <a href="#" class="dropdown-item">View calendar</a>
                                            </div>
                                        </div> -->
                                        <?php if($num_rows > 0) { ?>
                                        <a onclick="javascript:document.querySelector('#addAccountForm').style.display = 'block';" class="btn btn-tool btn-sm text-primary">
                                            <i class="fas fa-plus"></i>
                                            Add 
                                        </a>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php if($num_rows > 0) { ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Number</th>
                                                    <th>Bank</th>
                                                    <th>Currency</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($bank_accounts as $account) { ?>
                                                <tr>
                                                    <td><?= $account['account_name']; ?></td>
                                                    <td><?= $account['account_number']; ?></td>
                                                    <td><?= $account['bank_name']; ?></td>
                                                    <td><?= $account['currency']; ?></td>
                                                    <td>
                                                        <a href="delete.php?a=delete&t=account&id=<?= $account['id']; ?>&emp_id=<?= $emp_id; ?>" class="btn btn-tool btn-sm">
                                                            <i class="fas fa-minus text-danger"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php } else { ?>
                                        <p class="text-center">
                                            No Accounts yet <br>
                                            <a onclick="javascript:document.querySelector('#addAccountForm').style.display = 'block';" class="btn btn-primary btn-sm">Add Account</a>
                                        </p>
                                    <?php } ?>
                                    <div id="showAccountForm"></div>
        
                                    <div id="addAccountForm" style="display:none" class="card mt-3">
                                        <form method="post" class="card-body">
                                            <input type="hidden" name="company_id" value="<?= $emp['company_id']; ?>">
                                            <input type="hidden" name="employee_id" value="<?= $emp['id']; ?>">
                                            <div class="form-group mb-1">
                                                <label for="">Account Name:</label>
                                                <input type="text" name="account_name" id="" class="form-control" placeholder="Account Name">
                                            </div>
                                            <div class="form-group mb-1">
                                                <label for="">Account Number:</label>
                                                <input type="text" name="account_number" id="" class="form-control" placeholder="Account Number">
                                            </div>
                                            <div class="form-group mb-1">
                                                <label for="">Bank:</label>
                                                <input type="text" name="bank_name" id="" class="form-control" placeholder="Bank Name">
                                            </div>
                                            <div class="form-group mb-1">
                                                <label for="">Currency:</label>
                                                <select name="currency" id="" class="form-control">
                                                    <option value="NGN">NGN</option>
                                                    <option value="USD">USD</option>
                                                    <option value="GBP">GBP</option>
                                                    <option value="EUR">EUR</option>
                                                </select>
                                            </div>
                                            <div class="form-group mb-1 float-end">
                                                <a class="btn btn-danger" onclick="javascript:document.querySelector('#addAccountForm').style.display = 'none';">Cancel</a>
                                                <button type="submit" name="addAccountFormBtn" class="btn btn-success">Add Account</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bonuses -->
                    <div class="row mt-3 mb-3">
                        <?php $bonuses = $conn->query("SELECT * FROM employee_bonuses WHERE company_id = $company_id AND employee_id = $emp_id"); ?>
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Bonuses</h3>
                                    <div class="card-tools">
                                        <button type="button" onclick="javascript:document.querySelector('#addBonusForm').style.display = 'block';" class="btn btn-tool btn-sm text-primary">
                                            <i class="fas fa-plus"></i> 
                                            Add 
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php if($bonuses->num_rows == 0) { echo 'No Bonus record for this month'; } else { ?>
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Month</th>
                                                        <th>Amount</th>
                                                        <th>Reason</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($bonuses as $bonus) { ?>
                                                    <tr>
                                                        <td><?= $bonus['month']; ?></td>
                                                        <td><?= $bonus['amount']; ?></td>
                                                        <td><?= $bonus['reason']; ?></td>
                                                        <td>
                                                            <a href="delete.php?a=delete&t=bonus&id=<?= $bonus['id']; ?>&emp_id=<?= $emp_id; ?>" class="btn btn-tool btn-sm">
                                                                <i class="fas fa-minus text-danger"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div id="addBonusForm" style="display:none" class="card mt-3">
                                        <form method="post" class="card-body">
                                            <input type="hidden" name="company_id" value="<?= $emp['company_id']; ?>">
                                            <input type="hidden" name="employee_id" value="<?= $emp['id']; ?>">
                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-group mb-1">
                                                        <input type="month" name="month" class="form-control" placeholder="Month: ">
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="form-group mb-1">
                                                        <input type="number" step="0.01" name="amount" id="" class="form-control" placeholder="Amount: e.g: 0.00">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group mb-1">
                                                <textarea name="reason" class="form-control" cols="30" rows="3" placeholder="Reason: (Optional)"></textarea>
                                            </div>
                                            <div class="form-group mb-1 float-end">
                                                <a class="btn btn-danger" onclick="javascript:document.querySelector('#addBonusForm').style.display = 'none';">Cancel</a>
                                                <button type="submit" name="addBonusFormBtn" class="btn btn-success">Add</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Deductions -->
                    <div class="row mt-3 mb-3">
                        <?php $deductions = $conn->query("SELECT * FROM employee_deductions WHERE company_id = $company_id AND employee_id = $emp_id"); ?>
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header ui-sortable-handle">
                                    <h3 class="card-title">Deductions</h3>
                                    <!-- <div class="card-tools">
                                        <span title="3 New Messages" class="badge badge-primary">3</span>
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" title="Contacts" data-widget="chat-pane-toggle">
                                            <i class="fas fa-comments"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div> -->
                                    <div class="card-tools">
                                        <button type="button" onclick="javascript:document.querySelector('#addDeductionsForm').style.display = 'block';" class="btn btn-tool btn-sm text-primary">
                                            <i class="fas fa-plus"></i>
                                            Add
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php if($deductions->num_rows == 0) { echo '<span class="text-center">No Deduction record for this month</span>'; } else { ?>
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Month</th>
                                                        <th>Amount</th>
                                                        <th>Note</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($deductions as $deduction) { ?>
                                                    <tr>
                                                        <td><?= $deduction['month']; ?></td>
                                                        <td><?= $deduction['amount']; ?></td>
                                                        <td><?= $deduction['notes']; ?></td>
                                                        <td>
                                                            <a href="delete.php?a=delete&t=deduction&id=<?= $deduction['id']; ?>&emp_id=<?= $emp_id; ?>" class="btn btn-tool btn-sm text-danger">
                                                                <i class="fas fa-minus"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div id="addDeductionsForm" style="display:none" class="card mt-3">
                                        <form method="post" class="card-body">
                                            <input type="hidden" name="company_id" value="<?= $emp['company_id']; ?>">
                                            <input type="hidden" name="employee_id" value="<?= $emp['id']; ?>">
                                            <div class="form-group mb-1">
                                                <input type="text" name="title" class="form-control" placeholder="Deduction Title: ">
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-group mb-1">
                                                        <input type="month" name="month" class="form-control" placeholder="Month: ">
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="form-group mb-1">
                                                        <input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount: e.g: 0.00">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group mb-1">
                                                <textarea name="notes" class="form-control" cols="30" rows="3" placeholder="Notes: (Options)"></textarea>
                                            </div>
                                            <div class="form-group mb-1 float-end">
                                                <a class="btn btn-danger" onclick="javascript:document.querySelector('#addDeductionsForm').style.display = 'none';">Cancel</a>
                                                <button type="submit" name="addDeductionsFormBtn" class="btn btn-success">Add</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Allowances -->
                    <div class="row mt-3 mb-3">
                        <?php $allowances = $conn->query("SELECT * FROM employee_allowances WHERE company_id = $company_id AND employee_id = $emp_id"); ?>
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header ui-sortable-handle">
                                    <h3 class="card-title">Allowances</h3>
                                    <div class="card-tools">
                                        <button type="button" onclick="javascript:document.querySelector('#addAllowancesForm').style.display = 'block';" class="btn btn-tool btn-sm text-primary">
                                            <i class="fas fa-plus"></i>
                                            Add
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php if($allowances->num_rows == 0) { echo '<span class="text-center">No Allowance record for this month</span>'; } else { ?>
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Month</th>
                                                        <th>Amount</th>
                                                        <th>Note</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($allowances as $allowance) { ?>
                                                    <tr>
                                                        <td><?= $allowance['month']; ?></td>
                                                        <td><?= $allowance['amount']; ?></td>
                                                        <td><?= $allowance['notes']; ?></td>
                                                        <td>
                                                            <a href="delete.php?a=delete&t=allowance&id=<?= $allowance['id']; ?>&emp_id=<?= $emp_id; ?>" class="btn btn-tool btn-sm text-danger">
                                                                <i class="fas fa-minus"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div id="addAllowancesForm" style="display:none" class="card mt-3">
                                        <form method="post" class="card-body form-horizontal">
                                            <input type="hidden" name="company_id" value="<?= $emp['company_id']; ?>">
                                            <input type="hidden" name="employee_id" value="<?= $emp['id']; ?>">
                                            <div class="form-group mb-1">
                                                <input type="text" name="title" class="form-control" placeholder="Allowance Title: ">
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-group mb-1">
                                                        <input type="month" name="month" class="form-control" placeholder="Month: ">
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="form-group mb-1">
                                                        <input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount: e.g: 0.00">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group mb-1">
                                                <textarea name="notes" class="form-control" cols="30" rows="3" placeholder="Notes: (Optional)"></textarea>
                                            </div>
                                            <div class="form-group mb-1 float-end">
                                                <a class="btn btn-danger" onclick="javascript:document.querySelector('#addAllowancesForm').style.display = 'none';">Cancel</a>
                                                <button type="submit" name="addAllowancesFormBtn" class="btn btn-success">Add</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3"></div>
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
