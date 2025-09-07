<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$can_access = ['admin', 'superadmin', 'system'];
if(!in_array($_SESSION['user_role'], $can_access)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$company_id = $_SESSION['company_id'];

if(isset($_POST['generate'])) {
    $month = $_POST['month'];
    $employees = $conn->query("SELECT * FROM employees WHERE company_id = $company_id AND status='active'");

    
    
    // ----- Sample Tax Logic -----
    function calculate_tax($gross) {
      if ($gross <= 30000) return $gross * 0.05;
      elseif ($gross <= 100000) return 1500 + ($gross - 30000) * 0.10;
      elseif ($gross <= 200000) return 8500 + ($gross - 100000) * 0.15;
      else return 23500 + ($gross - 200000) * 0.20;
    }
    
    // ----- NIN/Social Contribution -----
    function calculate_nin_contribution($gross) {
      return $gross * 0.075; // 7.5% for pension/social security
    }
    
    foreach ($employees as $emp) {
      $emp_id = $emp['id'];
      $basic_salary = $emp['salary'];
      $allowances = $emp['other_deductions'];
      $deductions = $emp['other_deductions'];
      
      // Fetch country rules
      $rule = $conn->query("SELECT c.tax_rate, c.social_security_rate, c.currency_symbol 
      FROM countries c 
      JOIN employees e ON e.country_id = c.id 
      WHERE e.id = $emp_id")->fetch_assoc();
  
      // $tax_rate = $rule['tax_rate'];
      // $social_rate = $rule['social_security_rate'];
      // $currency = $rule['currency_symbol'];
  
      // // Apply calculations
      // $tax = $gross_salary * ($tax_rate / 100);
      // $social = $gross_salary * ($social_rate / 100);
      // $net_salary = $gross_salary - $tax - $social;


      // Dummy logic: Pull bonuses, deductions
      $bonus = 1000; // from bonus table (simplified)
      // $deduction = 500; // from attendance/tax (simplified)
      
      //$net = ($basic + $allowances + $bonus) - $deduction;
      
      // ----- Calculating number of days worked -----
        // Calculate unpaid leave deductions

        // $unpaid_days = 0;
        $check = $conn->query("SELECT COUNT(*) AS count FROM attendance WHERE company_id = $company_id AND employee_id = $emp_id AND MONTH(date) = MONTH('$month') AND status = 'Absent'");
        if ($row = $check->fetch_assoc()) {
            $unpaid_days = $row['count'];
        }
        $daily_rate = $basic_salary / 22; // assuming 22 workdays/month
        $leave_deduction = $unpaid_days * $daily_rate;

        $deductions += $leave_deduction;

        // ------ /end calculating number of days worked

        // ------ begin recording overtime and bonus ------
        // Overtime Calculation
        $ot = $conn->query("SELECT SUM(hours * rate) AS ot_pay FROM overtime_records 
        WHERE employee_id=$emp_id AND MONTH(date) = MONTH('$month') AND approved=1");
        $overtime_pay = ($row = $ot->fetch_assoc()) ? $row['ot_pay'] : 0;

        // Bonus
        $bonus_q = $conn->query("SELECT SUM(amount) AS bonus FROM employee_bonuses WHERE employee_id=$emp_id AND month = '$month'")->fetch_assoc();
        $bonus_amount = $bonus_q['bonus'] ?? 0;

        $gross_salary = $basic_salary + $allowances + $overtime_pay + $bonus_amount;
        // ------ / end recording overtime and bonus ------

        //$gross_salary = $basic + $allowances + $bonuses;

        $tax = calculate_tax($gross_salary);
        $nin_contrib = calculate_nin_contribution($gross_salary);
        $total_deductions = $deductions + $tax + $nin_contrib;

        $net = $gross_salary - $total_deductions;


        $check = $conn->query("SELECT * FROM payslips WHERE employee_id=$emp_id AND company_id = $company_id AND month='$month'");
        if ($check->num_rows == 0) {
            // $stmt = $conn->prepare("INSERT INTO payslips (employee_id, month, basic_salary, allowances, bonuses, deductions, net_salary) VALUES (?, ?, ?, ?, ?, ?, ?)");
            // $stmt->bind_param("issdddd", $emp_id, $month, $basic, $allowances, $bonus, $deduction, $net);
            // $stmt->execute();

            $stmt = $conn->prepare("INSERT INTO payslips (company_id, employee_id, month, basic_salary, allowances, bonuses, deductions, tax_deduction, nin_contribution, net_salary) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iisddddddd", $company_id, $emp_id, $month, $basic_salary, $allowances, $bonus_amount, $deductions, $tax, $nin_contrib, $net);
            $stmt->execute();

        }
    }

    echo "<div class='alert alert-success mt-3'>Payslips generated for $month.</div>";
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Generate Payslip</title>
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
            <div class="content-header mt-3 mb-3">
              <div class="row">
                <div class="col-md-6">
                  <h3>Generate Payslips</h3>
                </div>
                <div class="col-md-6">
                  <div class="float-end">
                    
                  </div>
                </div>
              </div>
            </div>

            <div class="content">
              <div class="card">
                <div class="card-body">
                  <div class="center">
                    <form method="post" class="row">
                      <div class="col-auto">
                        <label for="month" class="control-label">Select Month: </label>
                      </div>
                      <div class="col-auto">
                        <input type="month" name="month" class="form-control" required>
                      </div>
                      <div class="col-md-4">
                        <button type="submit" name="generate" class="btn btn-primary">Generate Payslips</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
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
