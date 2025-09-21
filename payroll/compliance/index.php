<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$company_id = $_SESSION['company_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employee_id = 0;
    $name = $_POST['name'];
    $code = $_POST['currency_code'];
    $symbol = $_POST['currency_symbol'];
    $tax = $_POST['tax_rate'];
    $wage = $_POST['minimum_wage'];

    $stmt = $conn->prepare("INSERT INTO employee_tax_compliance (company_id, employee_id, country, currency_code, currency_symbol, tax_rate, minimum_wage) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssdd", $company_id, $employee_id, $name, $code, $symbol, $tax, $wage);
    $stmt->execute();
    $msg = "Country added.";
}

$compliance = $conn->query("SELECT * FROM employee_tax_compliance WHERE company_id = $company_id");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Compliance</title>
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
                    <div class="content-header mt-4 mb-4">
                        <h4>Employee Tax Compliance</h4>
                    </div>
                    <div class="content">
                        <div class="row">
                            <div class="col-4">
                                <?php if (isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">New Tax Compliance</h3>
                                    </div>
                                    <div class="card-body">
                                        <form method="post" class="form-horizontal">
                                            <div class="form-group row mb-3">
                                                <div class="col">
                                                    <input type="text" name="name" class="form-control" placeholder="Country Name" required>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-3">
                                                <div class="col">
                                                    <input type="text" name="currency_code" class="form-control" placeholder="Currency Code (e.g. USD)" required>
                                                </div>
                                                <div class="col">
                                                    <input type="text" name="currency_symbol" class="form-control" placeholder="Symbol (e.g. $)" required>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-3">
                                                <div class="col">
                                                    <label for="tax_rate">Tax Rate:</label>
                                                    <input type="number" step="0.01" name="tax_rate" class="form-control" placeholder="Flat Tax Rate (%)" required>
                                                </div>
                                                <div class="col-auto">
                                                    <label for="minimum_wage">Minimum Wage:</label>
                                                    <input type="number" step="0.01" name="minimum_wage" class="form-control" placeholder="Minimum Wage" required>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary mt-3 float-end"><i class="bi bi-save"></i> Add</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Tax Compliance</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover DataTable">
                                                <thead>
                                                    <tr>
                                                        <th>Country</th>
                                                        <th>Currency</th>
                                                        <th>Tax %</th>
                                                        <th>Min Wage</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($compliance as $c): ?>
                                                        <tr>
                                                            <td><?= $c['country'] ?></td>
                                                            <td><?= $c['currency_code']; ?> (<?= $c['currency_symbol']; ?>)</td>
                                                            <td><?= $c['tax_rate'] ?>%</td>
                                                            <td><?= $c['minimum_wage'] ?></td>
                                                            <td>
                                                                <i class="bi b-times"></i>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
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