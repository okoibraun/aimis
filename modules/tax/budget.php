<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Fetch encumbrances + funds
$company_id = $_SESSION['company_id'];
$encumbrances = $conn->query("
  SELECT be.*, f.fund_name, f.fund_code 
  FROM tax_budget_encumbrance be
  JOIN tax_funds f ON be.fund_id = f.id
  WHERE f.company_id = $company_id
");

// For dropdown
$fundList = $conn->query("SELECT * FROM tax_funds WHERE company_id = $company_id");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Tax - Encumbrances</title>
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
                <!-- <h1><i class="fas fa-coins"></i> Budget Encumbrance Tracking</h1> -->
              </section>
              
              <section class="content mt-3">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">
                      <i class="fas fa-coins"></i> Budget Encumbrance Tracking
                    </h3>
                    <div class="card-tools">
                      <a href="index.php" class="btn btn-secondary btn-sm">Back</a>
                      <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#encumbranceModal">
                        <i class="fas fa-plus"></i> Add Encumbrance
                      </button>
                    </div>
                  </div>
                  <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Fund</th>
                          <th>Amount</th>
                          <th>Purpose</th>
                          <th>Encumbered Date</th>
                          <th>Released</th>
                          <th>Status</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($encumbrances as $i => $row): ?>
                          <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($row['fund_name']) ?> (<?= $row['fund_code'] ?>)</td>
                            <td>₦<?= number_format($row['amount'], 2) ?></td>
                            <td><?= htmlspecialchars($row['purpose']) ?></td>
                            <td><?= $row['encumbered_date'] ?></td>
                            <td><?= $row['released_date'] ?: '-' ?></td>
                            <td><span class="badge badge-<?= $row['status'] == 'encumbered' ? 'info' : ($row['status'] == 'released' ? 'success' : 'secondary') ?>">
                              <?= ucfirst($row['status']) ?>
                            </span></td>
                            <td>
                              <button class="btn btn-sm btn-info editBtn"
                                      data-id="<?= $row['id'] ?>"
                                      data-fund="<?= $row['fund_id'] ?>"
                                      data-amount="<?= $row['amount'] ?>"
                                      data-purpose="<?= $row['purpose'] ?>"
                                      data-date="<?= $row['encumbered_date'] ?>"
                                      data-release="<?= $row['released_date'] ?>"
                                      data-status="<?= $row['status'] ?>"
                                      data-toggle="modal" data-target="#encumbranceModal">
                                Edit
                              </button>
                              <a href="ajax/delete_encumbrance.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?')">Delete</a>
                            </td>
                          </tr>
                        <?php endforeach ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </section>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="encumbranceModal" tabindex="-1" role="dialog">
              <div class="modal-dialog" role="document">
                <form action="ajax/save_encumbrance.php" method="POST" id="encForm">
                  <input type="hidden" name="id" id="enc_id">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Encumbrance Details</h5>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                      <div class="form-group">
                        <label>Fund</label>
                        <select name="fund_id" id="enc_fund" class="form-control" required>
                          <option value="">-- Select Fund --</option>
                          <?php foreach ($fundList as $fund): ?>
                            <option value="<?= $fund['id'] ?>"><?= htmlspecialchars($fund['fund_name']) ?> (<?= $fund['fund_code'] ?>)</option>
                          <?php endforeach ?>
                        </select>
                      </div>
                      <div class="form-group">
                        <label>Amount</label>
                        <input type="number" name="amount" id="enc_amount" step="0.01" class="form-control" required>
                      </div>
                      <div class="form-group">
                        <label>Purpose</label>
                        <input type="text" name="purpose" id="enc_purpose" class="form-control" required>
                      </div>
                      <div class="form-group">
                        <label>Encumbered Date</label>
                        <input type="date" name="enc_date" id="enc_date" class="form-control" required>
                      </div>
                      <div class="form-group">
                        <label>Released Date</label>
                        <input type="date" name="rel_date" id="enc_release" class="form-control">
                      </div>
                      <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="enc_status" class="form-control">
                          <option value="encumbered">Encumbered</option>
                          <option value="released">Released</option>
                          <option value="expired">Expired</option>
                        </select>
                      </div>
                    </div>

                    <div class="modal-footer">
                      <button type="submit" class="btn btn-success">Save Entry</button>
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
      document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', function () {
          document.getElementById('enc_id').value = this.dataset.id;
          document.getElementById('enc_fund').value = this.dataset.fund;
          document.getElementById('enc_amount').value = this.dataset.amount;
          document.getElementById('enc_purpose').value = this.dataset.purpose;
          document.getElementById('enc_date').value = this.dataset.date;
          document.getElementById('enc_release').value = this.dataset.release;
          document.getElementById('enc_status').value = this.dataset.status;
        });
      });
    </script>
  </body>
  <!--end::Body-->
</html>
