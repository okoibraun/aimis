<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

// Fetch all tax configs for current company
$company_id = $_SESSION['company_id'] ?? 0; // Ensure company_id is set, default to 0 if not
$taxes = $conn->query("SELECT * FROM tax_config WHERE company_id = $company_id");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Tax - Setup</title>
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
                <h1><i class="fas fa-percentage"></i> Tax Configuration (VAT / GST / WHT)</h1>
              </section>

              <section class="content">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">
                      <i class="fas fa-percentage"></i> Tax Configuration (VAT / GST / WHT)
                    </h3>
                    <div class="card-tools">
                      <a href="index.php" class="btn btn-secondary btn-sm">Back</a>
                      <!-- Earlier versions of bootstrap <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#taxModal">
                        <i class="fas fa-plus"></i> Add Tax Rule
                      </button> -->
                      <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#taxModal">
                        <i class="fas fa-plus"></i> Add Tax Rule
                      </button>
                    </div>
                  </div>
                  <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Type</th>
                          <th>Rate (%)</th>
                          <th>Description</th>
                          <th>Status</th>
                          <th>Created</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($taxes as $i => $row): ?>
                          <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= $row['tax_type'] ?></td>
                            <td><?= number_format($row['rate'], 2) ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td>
                              <span class="badge badge-<?= $row['is_active'] ? 'success' : 'secondary' ?>">
                                <?= $row['is_active'] ? 'Active' : 'Inactive' ?>
                              </span>
                            </td>
                            <td><?= $row['created_at'] ?></td>
                            <td>
                              <button class="btn btn-sm btn-info editTaxBtn" data-id="<?= $row['id'] ?>" data-type="<?= $row['tax_type'] ?>" data-rate="<?= $row['rate'] ?>"
                                      data-desc="<?= $row['description'] ?>"
                                      data-active="<?= $row['is_active'] ?>"
                                      data-bs-toggle="modal" data-bs-target="#taxModal">
                                Edit
                              </button>
                              <a href="ajax/delete_tax.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this tax rule?')">Delete</a>
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
            <div class="modal fade" id="taxModal" tabindex="-1" role="dialog">
              <div class="modal-dialog" role="document">
                <form action="ajax/save_tax.php" method="POST">
                  <input type="hidden" name="id" id="tax_id">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Tax Rule</h5>
                      <!-- Earlier versions of bootstrap <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button> -->
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                      <div class="form-group">
                        <label>Tax Type</label>
                        <select name="tax_type" id="tax_type" class="form-control" required>
                          <option value="VAT">VAT</option>
                          <option value="GST">GST</option>
                          <option value="WHT">Withholding Tax</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label>Rate (%)</label>
                        <input type="number" name="rate" id="tax_rate" step="0.01" class="form-control" required>
                      </div>
                      <div class="form-group">
                        <label>Description</label>
                        <input type="text" name="description" id="tax_desc" class="form-control">
                      </div>
                      <div class="form-group">
                        <label>Status</label>
                        <select name="is_active" id="tax_active" class="form-control">
                          <option value="1">Active</option>
                          <option value="0">Inactive</option>
                        </select>
                      </div>
                    </div>

                    <div class="modal-footer">
                      <!-- Earlier versions of bootstrap <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-success">Save Tax</button>
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
      <?php include("../../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../../includes/scripts.phtml"); ?>
    <!--end::Script-->
    <script>
      document.querySelectorAll('.editTaxBtn').forEach(btn => {
        btn.addEventListener('click', function () {
          document.getElementById('tax_id').value = this.dataset.id;
          document.getElementById('tax_type').value = this.dataset.type;
          document.getElementById('tax_rate').value = this.dataset.rate;
          document.getElementById('tax_desc').value = this.dataset.desc;
          document.getElementById('tax_active').value = this.dataset.active;
        });
      });

      toastr.info("This is how to display info Toastr");
      toastr.success("This is how to display success Toastr");
      toastr.warning("This is how to display warning Toastr");
      toastr.error("This is how to display error Toastr");
      toastr.success('We do have the Kapua suite available.', 'Turtle Bay Resort', {timeOut: 5000});
    </script>
  </body>
  <!--end::Body-->
</html>
