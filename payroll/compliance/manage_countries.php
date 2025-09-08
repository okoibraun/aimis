<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $code = $_POST['currency_code'];
    $symbol = $_POST['currency_symbol'];
    $tax = $_POST['tax_rate'];
    $ss = $_POST['social_security_rate'];
    $wage = $_POST['minimum_wage'];

    $stmt = $conn->prepare("INSERT INTO countries (name, currency_code, currency_symbol, tax_rate, social_security_rate, minimum_wage) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssddd", $name, $code, $symbol, $tax, $ss, $wage);
    $stmt->execute();
    $msg = "Country added.";
}

$countries = $conn->query("SELECT * FROM countries");
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

            <h4>Manage Countries</h4>
            <?php if (isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>
            <form method="post">
                <div class="form-row">
                    <div class="col">
                        <input type="text" name="name" class="form-control" placeholder="Country Name" required>
                    </div>
                    <div class="col">
                        <input type="text" name="currency_code" class="form-control" placeholder="Currency Code (e.g. USD)" required>
                    </div>
                    <div class="col">
                        <input type="text" name="currency_symbol" class="form-control" placeholder="Symbol (e.g. $)" required>
                    </div>
                </div>
                <div class="form-row mt-2">
                    <div class="col">
                        <input type="number" step="0.01" name="tax_rate" class="form-control" placeholder="Flat Tax Rate (%)" required>
                    </div>
                    <div class="col">
                        <input type="number" step="0.01" name="social_security_rate" class="form-control" placeholder="Social Security (%)" required>
                    </div>
                    <div class="col">
                        <input type="number" step="0.01" name="minimum_wage" class="form-control" placeholder="Minimum Wage" required>
                    </div>
                </div>
                <button class="btn btn-primary mt-3">Add Country</button>
            </form>

            <hr>
            <h5>Configured Countries</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th><th>Currency</th><th>Tax %</th><th>SS %</th><th>Min Wage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($c = $countries->fetch_assoc()): ?>
                        <tr>
                            <td><?= $c['name'] ?></td>
                            <td><?= $c['currency_code'] ?> (<?= $c['currency_symbol'] ?>)</td>
                            <td><?= $c['tax_rate'] ?>%</td>
                            <td><?= $c['social_security_rate'] ?>%</td>
                            <td><?= $c['minimum_wage'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

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