<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

$company_id = $_SESSION['company_id'];
$configs = $conn->query("SELECT * FROM tax_api_config WHERE company_id = $company_id");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Tax - API Config</title>
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
                <h1><i class="fas fa-cogs"></i> Country-Specific Tax API Configurations</h1>
              </section>

              <section class="content">
                <div class="card">
                  <div class="card-body">
                    <form method="POST" action="ajax/save_api_config.php">
                      <div class="row">
                        <div class="form-group col-md-3">
                          <label>Country</label>
                          <input name="country" class="form-control" required>
                        </div>
                        <div class="form-group col-md-3">
                          <label>Authority</label>
                          <input name="authority_name" class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                          <label>API Endpoint</label>
                          <input name="api_endpoint" class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                          <label>API Token</label>
                          <input name="api_token" class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                          <label>Environment</label>
                          <select name="environment" class="form-control">
                            <option value="sandbox">Sandbox</option>
                            <option value="production">Production</option>
                          </select>
                        </div>
                      </div>
                      <button class="btn btn-primary"><i class="fas fa-save"></i> Save Configuration</button>
                    </form>
                  </div>
                </div>

                <div class="card">
                  <div class="card-header"><h3 class="card-title">Existing API Configs</h3></div>
                  <div class="card-body table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Country</th>
                          <th>Authority</th>
                          <th>API URL</th>
                          <th>Token</th>
                          <th>Env</th>
                          <th>Last Update</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($configs as $c): ?>
                          <tr>
                            <td><?= $c['country'] ?></td>
                            <td><?= $c['authority_name'] ?></td>
                            <td><?= $c['api_endpoint'] ?></td>
                            <td><?= substr($c['api_token'], 0, 10) ?>...</td>
                            <td><?= $c['environment'] ?></td>
                            <td><?= $c['updated_at'] ?></td>
                          </tr>
                        <?php endforeach ?>
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
