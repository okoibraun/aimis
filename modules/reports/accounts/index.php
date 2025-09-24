<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');
include("../../../functions/role_functions.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

// Get filters
$account_id = isset($_GET['account_id']) ? intval($_GET['account_id']) : 0;
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Accounts - Report</title>
    <?php include_once("../../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">

            <div class="content-wrapper">
                <section class="content-header mt-3 mb-3">
                  <h1>Accounts Report</h1>
                </section>

                <section class="content">
                  <div class="card">
                    <div class="card-header">
                      <h4 class="card-title">Filter Report</h4>
                      <div class="card-tools">
                        <form method="GET" class="row">
                          <div class="col">
                            <select name="account_id" class="form-control mx-2" required>
                              <option value="">-- Select Account --</option>
                              <?php 
                              $accounts = $conn->query("SELECT id, account_name FROM accounts WHERE company_id = $company_id");
                              foreach($accounts as $account) { $selected = ($account['id'] == $account_id) ? 'selected' : '';
                              ?>
                              <option value="<?= $account['id'] ?>" $selected><?= $account['account_name'] ?></option>
                              <?php } ?>
                            </select>
                          </div>
                          <div class="col-auto">
                            <input type="date" name="from" value="<?= $from ?>" class="form-control mx-2">
                          </div>
                          <div class="col-auto">
                            <input type="date" name="to" value="<?= $to ?>" class="form-control mx-2">
                          </div>
                          <div class="col-auto">
                            <button type="submit" class="btn btn-primary">View Report</button>
                          </div>
                        </form>
                      </div>
                    </div>
                    <div class="card-body table-responsive">
                      <?php if ($account_id): ?>
                        <table class="table table-hover table-striped">
                          <thead>
                            <tr>
                              <th>Date</th>
                              <th>Description</th>
                              <th>Debit</th>
                              <th>Credit</th>
                              <th>Running Balance</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            $query = "SELECT je.entry_date, je.description, jl.debit, jl.credit
                                      FROM journal_lines jl
                                      JOIN journal_entries je ON jl.journal_entry_id = je.id
                                      WHERE jl.account_id = $account_id AND je.company_id = $company_id";
    
                            if ($from) $query .= " AND je.entry_date >= '$from'";
                            if ($to) $query .= " AND je.entry_date <= '$to'";
    
                            $query .= " ORDER BY je.entry_date ASC";
    
                            $res = $conn->query($query);
                            $balance = 0;
                            foreach($res as $row) {
                              $balance += $row['debit'] - $row['credit'];
                            ?>
                            <tr>
                              <td><?= $row['entry_date'] ?></td>
                              <td><?= $row['description'] ?></td>
                              <td><?= $row['debit'] ?></td>
                              <td><?= $row['credit'] ?></td>
                              <td><?= $balance ?></td>
                            </tr>
                            <?php } ?>
                          </tbody>
                        </table>
                      <?php endif; ?>
                    </div>
                  </div>
                  
                </section>
            </div>
            

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>