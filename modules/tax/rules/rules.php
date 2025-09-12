<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

// Fetch existing rules
$company_id = $_SESSION['company_id'] ?? 0; // Ensure company_id is set
$rules = $conn->query("SELECT * FROM intl_tax_rules WHERE company_id = $company_id");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Tax - Rules</title>
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
                <h3>Tax - Rules</h3>
                <!-- <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#ruleModal">
                  <i class="fas fa-plus"></i> Add Rule
                </button> -->
              </section>

              <section class="content mt-3">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">
                      <!-- <i class="fas fa-globe"></i> Country-Specific Tax Templates & BEPS Compliance -->
                       Rules
                    </h3>
                    <div class="card-tools">
                      <a href="../" class="btn btn-secondary btn-sm">Back</a>
                      <a href="add.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Rule
                      </a>
                    </div>
                  </div>
                  <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Country</th>
                          <th>Template</th>
                          <th>BEPS Compliant</th>
                          <th>Status</th>
                          <th>Updated</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($rules as $i => $r): ?>
                          <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= $r['country'] ?></td>
                            <td><?= $r['template_name'] ?></td>
                            <td>
                              <?= $r['beps_compliant'] ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-warning">No</span>' ?>
                            </td>
                            <td>
                              <span class="badge badge-<?= $r['is_active'] ? 'success' : 'secondary' ?>">
                                <?= $r['is_active'] ? 'Active' : 'Inactive' ?>
                              </span>
                            </td>
                            <td><?= $r['updated_at'] ?></td>
                            <td>
                              <a href="edit.php?id<?= $r['id'] ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                              </a>
                              <a href="delete.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this rule?')">
                                <i class="fas fa-trash"></i>
                              </a>
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
            <div class="modal fade" id="ruleModal" tabindex="-1" role="dialog">
              <div class="modal-dialog" role="document">
                <form action="ajax/save_rule.php" method="POST">
                  <input type="hidden" name="id" id="rule_id">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Country-Specific Rule</h5>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                      <div class="form-group">
                        <label>Country</label>
                        <input type="text" name="country" id="rule_country" class="form-control" required>
                      </div>
                      <div class="form-group">
                        <label>Template Name</label>
                        <input type="text" name="template_name" id="rule_template" class="form-control" required>
                      </div>
                      <div class="form-group">
                        <label>BEPS Compliant?</label>
                        <select name="beps_compliant" id="rule_beps" class="form-control">
                          <option value="1">Yes</option>
                          <option value="0">No</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label>Status</label>
                        <select name="is_active" id="rule_active" class="form-control">
                          <option value="1">Active</option>
                          <option value="0">Inactive</option>
                        </select>
                      </div>
                    </div>

                    <div class="modal-footer">
                      <button type="submit" class="btn btn-success">Save Rule</button>
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
      document.querySelectorAll('.editRuleBtn').forEach(btn => {
        btn.addEventListener('click', function () {
          document.getElementById('rule_id').value = this.dataset.id;
          document.getElementById('rule_country').value = this.dataset.country;
          document.getElementById('rule_template').value = this.dataset.template;
          document.getElementById('rule_beps').value = this.dataset.beps;
          document.getElementById('rule_active').value = this.dataset.active;
        });
      });
    </script>
  </body>
  <!--end::Body-->
</html>
