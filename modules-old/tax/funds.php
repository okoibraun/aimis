<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Fetch funds
$company_id = $_SESSION['company_id'];
$funds = $conn->prepare("SELECT * FROM tax_funds WHERE company_id = $company_id");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Tax - Funds</title>
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
                <section class="content-header">
                    <!-- <h1><i class="fas fa-piggy-bank"></i> Fund Accounting</h1> -->
                </section>

                <section class="content mt-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-piggy-bank"></i> Fund Accounting
                            </h3>
                            <div class="card-tools">
                                <a href="index.php" class="btn btn-secondary btn-sm">Back</a>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#fundModal">
                                    <i class="fas fa-plus"></i> Add Fund
                                </button>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                <th>#</th>
                                <th>Fund Code</th>
                                <th>Name</th>
                                <th>Balance</th>
                                <th>Created</th>
                                <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($funds as $i => $fund): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($fund['fund_code']) ?></td>
                                    <td><?= htmlspecialchars($fund['fund_name']) ?></td>
                                    <td>₦<?= number_format($fund['balance'], 2) ?></td>
                                    <td><?= $fund['created_at'] ?></td>
                                    <td>
                                    <button class="btn btn-sm btn-info editFundBtn" 
                                            data-id="<?= $fund['id'] ?>" 
                                            data-code="<?= $fund['fund_code'] ?>" 
                                            data-name="<?= $fund['fund_name'] ?>" 
                                            data-balance="<?= $fund['balance'] ?>"
                                            data-toggle="modal" data-target="#fundModal">
                                        Edit
                                    </button>
                                    <a href="ajax/delete_fund.php?id=<?= $fund['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this fund?')">Delete</a>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Fund Modal -->
            <div class="modal fade" id="fundModal" tabindex="-1" role="dialog" aria-labelledby="fundModalLabel">
                <div class="modal-dialog" role="document">
                    <form action="ajax/save_fund.php" method="POST" id="fundForm">
                    <input type="hidden" name="id" id="fund_id">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h5 class="modal-title">Fund Details</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                        <div class="form-group">
                            <label>Fund Code</label>
                            <input type="text" name="fund_code" id="fund_code" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Fund Name</label>
                            <input type="text" name="fund_name" id="fund_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Opening Balance</label>
                            <input type="number" name="balance" id="fund_balance" class="form-control" step="0.01" required>
                        </div>
                        </div>
                        <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save Fund</button>
                        </div>
                    </div>
                    </form>
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
    <script>
    // Fill modal on edit
    document.querySelectorAll('.editFundBtn').forEach(btn => {
        btn.addEventListener('click', function () {
        document.getElementById('fund_id').value = this.dataset.id;
        document.getElementById('fund_code').value = this.dataset.code;
        document.getElementById('fund_name').value = this.dataset.name;
        document.getElementById('fund_balance').value = this.dataset.balance;
        });
    });
    </script>
  </body>
  <!--end::Body-->
</html>
