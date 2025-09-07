<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Fetch grants
$company_id = $_SESSION['company_id'];
$grants = $conn->query("SELECT * FROM tax_grants WHERE company_id = $company_id");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Tax - Grants</title>
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
              <!-- <section class="content-header">
                <h1><i class="fas fa-hand-holding-usd"></i> Grant & Aid Tracking</h1>
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#grantModal">
                  <i class="fas fa-plus"></i> Add Grant
                </button>
              </section> -->

              <section class="content mt-3">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">
                      <i class="fas fa-hand-holding-usd"></i> Grant & Aid Tracking
                    </h3>
                    <div class="card-tools">
                      <a href="index.php" class="btn btn-secondary btn-sm">Back</a>
                      <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#grantModal">
                        <i class="fas fa-plus"></i> Add Grant
                      </button>
                    </div>
                  </div>
                  <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Name</th>
                          <th>Source</th>
                          <th>Awarded</th>
                          <th>Spent</th>
                          <th>Start - End</th>
                          <th>Status</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($grants as $i => $g): ?>
                          <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($g['grant_name']) ?></td>
                            <td><?= htmlspecialchars($g['source']) ?></td>
                            <td>₦<?= number_format($g['amount_awarded'], 2) ?></td>
                            <td>₦<?= number_format($g['amount_spent'], 2) ?></td>
                            <td><?= $g['start_date'] ?> to <?= $g['end_date'] ?></td>
                            <td><?= ucfirst($g['status']) ?></td>
                            <td>
                              <button class="btn btn-sm btn-info editGrantBtn"
                                      data-id="<?= $g['id'] ?>"
                                      data-name="<?= $g['grant_name'] ?>"
                                      data-source="<?= $g['source'] ?>"
                                      data-awarded="<?= $g['amount_awarded'] ?>"
                                      data-spent="<?= $g['amount_spent'] ?>"
                                      data-start="<?= $g['start_date'] ?>"
                                      data-end="<?= $g['end_date'] ?>"
                                      data-status="<?= $g['status'] ?>"
                                      data-toggle="modal" data-target="#grantModal">
                                Edit
                              </button>
                              <a href="ajax/delete_grant.php?id=<?= $g['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this grant?')">Delete</a>
                            </td>
                          </tr>
                        <?php endforeach ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </section>
            </div>

            <!-- Grant Modal -->
            <div class="modal fade" id="grantModal" tabindex="-1" role="dialog">
              <div class="modal-dialog" role="document">
                <form action="ajax/save_grant.php" method="POST" id="grantForm">
                  <input type="hidden" name="id" id="grant_id">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Grant Details</h5>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                      <div class="form-group">
                        <label>Grant Name</label>
                        <input type="text" name="grant_name" id="grant_name" class="form-control" required>
                      </div>
                      <div class="form-group">
                        <label>Source</label>
                        <input type="text" name="source" id="grant_source" class="form-control">
                      </div>
                      <div class="form-group">
                        <label>Amount Awarded</label>
                        <input type="number" step="0.01" name="amount_awarded" id="grant_awarded" class="form-control" required>
                      </div>
                      <div class="form-group">
                        <label>Amount Spent</label>
                        <input type="number" step="0.01" name="amount_spent" id="grant_spent" class="form-control">
                      </div>
                      <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" name="start_date" id="grant_start" class="form-control">
                      </div>
                      <div class="form-group">
                        <label>End Date</label>
                        <input type="date" name="end_date" id="grant_end" class="form-control">
                      </div>
                      <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="grant_status" class="form-control">
                          <option value="active">Active</option>
                          <option value="completed">Completed</option>
                          <option value="cancelled">Cancelled</option>
                        </select>
                      </div>
                    </div>

                    <div class="modal-footer">
                      <button type="submit" class="btn btn-success">Save Grant</button>
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
      // Populate modal for edit
      document.querySelectorAll('.editGrantBtn').forEach(btn => {
        btn.addEventListener('click', function () {
          document.getElementById('grant_id').value = this.dataset.id;
          document.getElementById('grant_name').value = this.dataset.name;
          document.getElementById('grant_source').value = this.dataset.source;
          document.getElementById('grant_awarded').value = this.dataset.awarded;
          document.getElementById('grant_spent').value = this.dataset.spent;
          document.getElementById('grant_start').value = this.dataset.start;
          document.getElementById('grant_end').value = this.dataset.end;
          document.getElementById('grant_status').value = this.dataset.status;
        });
      });
    </script>
  </body>
  <!--end::Body-->
</html>
