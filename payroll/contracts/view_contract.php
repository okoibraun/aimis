<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$employee_id = isset($_GET['employee_id']) ? $_GET['employee_id'] : $_SESSION['employee_id'];
$company_id = $_SESSION['company_id'];
$contract = $conn->query("SELECT * FROM contracts WHERE company_id = $company_id AND employee_id = $employee_id ORDER BY id DESC LIMIT 1")->fetch_assoc();
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Payroll - View Employee Contracts</title>
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
          <section class="content-wrapper">

            <section class="content-header mt-3 mb-3">
              <h3>Employee Contracts</h3>
            </section>

            <section class="content">
              <?php if(isset($_GET['employee_id'])) { ?>
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="float-end">
                            <a href="manage_contract.php?emp_id=<?= $employee_id; ?>" class="btn btn-primary">Manage Contract</a>
                        </div>
                    </div>
                </div>
              <?php } ?>

              <div class="row">
                <div class="col">
                    <div class="card">
                      <div class="card-header">
                        <h3 class="card-title">Contract Details</h3>
                      </div>
    
                      <div class="card-body">
                          <?php if ($contract): ?>
                              <p><strong>Contract Type:</strong> <?= $contract['contract_type'] ?></p>
                              <p><strong>Start Date:</strong> <?= $contract['start_date'] ?></p>
                              <p><strong>End Date:</strong> <?= $contract['end_date'] ?: 'N/A' ?></p>
                              <p><strong>Status:</strong> <?= ucfirst($contract['status']) ?></p>
                              <p><strong>Terms:</strong><br><?= nl2br($contract['terms']) ?></p>
                          <?php else: ?>
                              <div class="alert alert-warning">No contract found for this employee.</div>
                          <?php endif; ?>
                      </div>
                    </div>
                </div>
                <div class="col-4">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">
                        Contract Documents/Files
                      </h3>
                      <div class="card-tools">
    
                      </div>
                    </div>
                    <div class="card-body">
                      <?php
                      $contract_id = isset($contract['id']) ?? 0;
                      $contract_files = $conn->query("SELECT * FROM contract_files WHERE company_id = $company_id AND contract_id = '$contract_id'"); 
                      ?>
                      <?= ($contract_files->num_rows == 0) ? "No contract files / documents" : ""; ?>
                      <ul class="list-group">
                        <?php foreach($contract_files as $file) { ?>
                        <li class="list-group-item"><a href="/uploads/contracts/<?= $file['file_link']; ?>"><?= $file['file_name']; ?></a></li>
                        <?php } ?>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </section>

          </section>

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