<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Accounts - Ledger Summary Report</title>
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
                        <a href="list_entries.php" class="btn btn-secondary">Back</a>
                    </div>
                </div>
            </div>

            <div class="content-wrapper">
                <section class="content-header">
                  <h1>Ledger Summary Report</h1>
                </section>

                <section class="content">
                  <form method="GET" class="form-inline mb-3">
                    <label>From:</label>
                    <input type="date" name="start_date" value="<?= $start_date ?>" class="form-control mx-2" required>
                    <label>To:</label>
                    <input type="date" name="end_date" value="<?= $end_date ?>" class="form-control mx-2" required>
                    <button type="submit" class="btn btn-primary">Generate Summary</button>
                  </form>

                  <div class="card">
                    <div class="card-body">
                      <table class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th>Account</th>
                            <th>Opening Balance</th>
                            <th>Total Debit</th>
                            <th>Total Credit</th>
                            <th>Closing Balance</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            $accounts = mysqli_query($conn, "SELECT id, account_name FROM accounts ORDER BY account_name");
                            while ($acct = mysqli_fetch_assoc($accounts)) {
                              $acct_id = $acct['id'];

                              // Opening balance = sum of all prior debits - credits
                              $opq = "
                                SELECT SUM(debit) as debit, SUM(credit) as credit
                                FROM journal_lines jl
                                JOIN journal_entries je ON jl.journal_entry_id = je.id
                                WHERE jl.account_id = $acct_id AND je.entry_date < '$start_date'
                              ";
                              $opr = mysqli_fetch_assoc(mysqli_query($conn, $opq));
                              $opening = ($opr['debit'] ?? 0) - ($opr['credit'] ?? 0);

                              // Total debit/credit in date range
                              $movq = "
                                SELECT SUM(debit) as debit, SUM(credit) as credit
                                FROM journal_lines jl
                                JOIN journal_entries je ON jl.journal_entry_id = je.id
                                WHERE jl.account_id = $acct_id AND je.entry_date BETWEEN '$start_date' AND '$end_date'
                              ";
                              $mov = mysqli_fetch_assoc(mysqli_query($conn, $movq));
                              $debit = $mov['debit'] ?? 0;
                              $credit = $mov['credit'] ?? 0;

                              $closing = $opening + $debit - $credit;

                              // Skip completely zero accounts
                              if ($opening == 0 && $debit == 0 && $credit == 0 && $closing == 0) continue;
                          ?>
                          <tr>
                            <td><?= htmlspecialchars($acct['account_name']) ?></td>
                            <td><?= number_format($opening, 2) ?></td>
                            <td><?= number_format($debit, 2) ?></td>
                            <td><?= number_format($credit, 2) ?></td>
                            <td><?= number_format($closing, 2) ?></td>
                          </tr>
                          <?php } ?>
                        </tbody>
                      </table>
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