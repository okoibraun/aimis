<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

$company_id = get_current_company_id();
$related_type = $_GET['related_type'];
$related_id = intval($_GET['related_id']);

$stmt = $conn->prepare("SELECT * FROM crm_communications WHERE company_id = ? AND related_type = ? AND related_id = ? ORDER BY created_at DESC");
$stmt->bind_param("isi", $company_id, $related_type, $related_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | CRM - Communications</title>
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
                      <h1 class="">CRM Communications</h1>
                    </div>
                    <div class="col-lg-6">
                      <ol class="breadcrumb float-end">
                        <li class="breadcrumb-item"><a href="../../../index.php"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="#">CRM</a></li>
                        <li class="breadcrumb-item active">Communications</li>
                      </ol>
                    </div>
                  </div>
                </section>

                <section class="content">
                  <div class="card">
                    <div class="card-header">
                      <div class="row">
                        <div class="col-lg-6">
                          <h3 class="card-title"><?= ucfirst($related_type) ?> Communication History</h3>
                        </div>
                        <div class="col-lg-6 text-end">
                          <a href="add.php?related_type=<?= $related_type ?>&related_id=<?= $related_id ?>" class="btn btn-primary btn-sm">Add Communication</a>
                        </div>
                      </div>
                    </div>
                    <div class="card-body">
                        <ul class="timeline">
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <li>
                            <i class="fa fa-comments bg-blue"></i>
                            <div class="timeline-item">
                                <span class="time">
                                    <i class="fa fa-clock"></i> <?= $row['created_at'] ?>
                                </span>
                                <h3 class="timeline-header">
                                    <?= ucfirst($row['communication_type']) ?>: <?= htmlspecialchars($row['subject']) ?>
                                    <span class="pull-right">
                                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i></a>
                                        <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete this communication entry?')"><i class="fa fa-trash"></i></a>
                                    </span>
                                </h3>

                                <div class="timeline-body">
                                    <?= nl2br(htmlspecialchars($row['details'])) ?>
                                </div>
                            </div>
                            </li>
                        <?php endwhile; ?>
                        </ul>
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
