<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$account_id = $_GET['account_id'] ?? '';
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

// Fetch all ledger accounts
$accounts = mysqli_query($conn, "SELECT id, account_name FROM accounts ORDER BY account_name");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Accounts - Ledger Report</title>
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
                  <h1>General Ledger Report</h1>
                </section>

                <section class="content">
                  <form method="GET" class="form-inline mb-3">
                    <label>Account:</label>
                    <select name="account_id" class="form-control mx-2" required>
                      <option value="">-- Select Account --</option>
                      <?php while ($row = mysqli_fetch_assoc($accounts)): ?>
                        <option value="<?= $row['id'] ?>" <?= $account_id == $row['id'] ? 'selected' : '' ?>>
                          <?= $row['account_name'] ?>
                        </option>
                      <?php endwhile; ?>
                    </select>

                    <label>From:</label>
                    <input type="date" name="start_date" value="<?= $start_date ?>" class="form-control mx-2" required>

                    <label>To:</label>
                    <input type="date" name="end_date" value="<?= $end_date ?>" class="form-control mx-2" required>

                    <button type="submit" class="btn btn-primary">Generate</button>
                  </form>

                  <?php if ($account_id): ?>
                    <div class="card">
                      <div class="card-body">
                        <h4>Ledger for Account ID: <?= $account_id ?></h4>
                        <table class="table table-bordered table-striped">
                          <thead>
                            <tr>
                              <th>Date</th>
                              <th>Memo</th>
                              <th>Debit</th>
                              <th>Credit</th>
                              <th>Running Balance</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                              $query = "
                                SELECT je.entry_date, je.memo, jl.debit, jl.credit
                                FROM journal_lines jl
                                JOIN journal_entries je ON je.id = jl.journal_entry_id
                                WHERE jl.account_id = $account_id
                                  AND je.entry_date BETWEEN '$start_date' AND '$end_date'
                                ORDER BY je.entry_date ASC
                              ";

                              $result = mysqli_query($conn, $query);
                              $running_balance = 0;

                              while ($row = mysqli_fetch_assoc($result)):
                                $running_balance += $row['debit'] - $row['credit'];
                            ?>
                              <tr>
                                <td><?= $row['entry_date'] ?></td>
                                <td><?= $row['memo'] ?></td>
                                <td><?= number_format($row['debit'], 2) ?></td>
                                <td><?= number_format($row['credit'], 2) ?></td>
                                <td><?= number_format($running_balance, 2) ?></td>
                              </tr>
                            <?php endwhile; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  <?php endif; ?>
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