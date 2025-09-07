<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

$company_id = get_current_company_id();
$stmt = $conn->prepare("SELECT c.*, co.name AS company_name FROM crm_contacts c LEFT JOIN crm_companies co ON c.company_id = co.id WHERE c.company_id = ?");
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();
$contacts = $result->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | CRM - Contacts</title>
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
              <div class="row">
                <div class="col-lg-6">
                  <h2 class="">CRM Contacts</h2>
                </div>
                <div class="col-lg-6 text-end">
                  <ol class="breadcrumb float-end">
                    <li class="breadcrumb-item"><a href="../../../index.php"><i class="fa fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item"><a href="#">CRM</a></li>
                    <li class="breadcrumb-item active">Contacts</li>
                  </ol>
                </div>
              </div>
            </section>

            <section class="content">
              <div class="card">
                <div class="card-header">
                  <div class="row">
                    <div class="col-lg-6">
                      <h3 class="card-title">Contacts</h3>
                    </div>
                    <div class="col-lg-6 text-end">
                      <a href="add.php" class="btn btn-primary btn-sm">+ Add Contact</a>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <table class="table table-bordered table-striped" id="contactsTable">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Company</th>
                        <th>Title</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($contacts as $contact): ?>
                      <tr>
                        <td><?= htmlspecialchars($contact['full_name']) ?></td>
                        <td><?= htmlspecialchars($contact['email']) ?></td>
                        <td><?= htmlspecialchars($contact['phone']) ?></td>
                        <td><?= htmlspecialchars($contact['company_name']) ?></td>
                        <td><?= htmlspecialchars($contact['job_title']) ?></td>
                        <td>
                          <a href="view.php?id=<?= $contact['id'] ?>" class="btn btn-xs btn-info">View</a>
                          <a href="edit.php?id=<?= $contact['id'] ?>" class="btn btn-xs btn-warning">Edit</a>
                          <a href="delete.php?id=<?= $contact['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete this contact?')">Delete</a>
                        </td>
                      </tr>
                      <?php endforeach; ?>
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
      <?php include("../../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../../includes/scripts.phtml"); ?>
    <!--end::Script-->
    <script>
      $(function () {
        $('#leadsTable').DataTable();
      });
    </script>
  </body>
  <!--end::Body-->
</html>
