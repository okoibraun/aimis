<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';
include("../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

// Check User Permissions
$page = "view";
$user_permissions = get_user_permissions($_SESSION['user_id']);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$id = intval($_GET['id']);

// Get company info
$stmt = $conn->prepare("SELECT * FROM crm_companies WHERE id = ? AND company_id = ?");
$stmt->bind_param("ii", $id, $company_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) die("Company not found.");
$company = $result->fetch_assoc();

// Get linked contacts
// $stmt2 = $conn->prepare("SELECT * FROM crm_contacts WHERE company_id = ? AND company_id IS NOT NULL AND company_id IN (SELECT id FROM crm_companies WHERE company_id = ?)");
// $stmt2->bind_param("ii", $id, $company_id);
// $stmt2->execute();
// $contacts = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
$contacts = $conn->query("SELECT * FROM crm_contacts WHERE company_id = $company_id AND crm_company_id = {$company['id']}")

$related_type = 'company';
$related_id = $company['id'];

$stmt = $conn->prepare("SELECT * FROM crm_communications WHERE company_id = ? AND related_type = ? AND related_id = ? ORDER BY created_at DESC");
$stmt->bind_param("isi", $company_id, $related_type, $related_id);
$stmt->execute();
$comm_result = $stmt->get_result();
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | CRM - Companies</title>
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
            <section class="content-header">
              <h1><?= htmlspecialchars($company['name']) ?></h1>
              <a href="edit.php?id=<?= $company['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
              <a href="./" class="btn btn-default btn-sm">Back to List</a>
            </section>

            <section class="content">
              <div class="row">
                <div class="col-md-5">
                  <div class="card card-info">
                    <div class="card-body">
                      <p><strong>Industry:</strong> <?= htmlspecialchars($company['industry']) ?></p>
                      <p><strong>Phone:</strong> <?= htmlspecialchars($company['phone']) ?></p>
                      <p><strong>Website:</strong> <a href="<?= htmlspecialchars($company['website']) ?>" target="_blank"><?= htmlspecialchars($company['website']) ?></a></p>
                      <p><strong>Notes:</strong><br><?= nl2br(htmlspecialchars($company['notes'])) ?></p>
                    </div>
                  </div>
                </div>
                <div class="col-md-7">
                  <div class="card card-primary">
                    <div class="card-header with-border"><h3 class="card-title">Related Contacts</h3></div>
                    <div class="card-body">
                      <?php if (empty($contacts)): ?>
                        <p class="text-muted">No contacts linked to this company.</p>
                      <?php else: ?>
                        <ul class="list-group">
                          <?php foreach ($contacts as $c): ?>
                            <li class="list-group-item">
                              <a href="../contacts/view.php?id=<?= $c['id'] ?>"><?= htmlspecialchars($c['full_name']) ?></a><br>
                              <?= htmlspecialchars($c['job_title']) ?> — <?= htmlspecialchars($c['email']) ?>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-12">

                  <div class="card">
                    <div class="card-header with-border">
                      <h3 class="card-title">Communication History</h3>
                      <a href="../communications/add.php?related_type=contact&related_id=<?= $contact['id'] ?>" class="btn btn-sm btn-primary pull-right">Add Entry</a>
                    </div>
                    <div class="card-body">
                      <?php if ($comm_result->num_rows > 0): ?>
                        <ul class="timeline">
                          <?php while ($row = $comm_result->fetch_assoc()): ?>
                            <li>
                              <i class="fa fa-comments bg-blue"></i>
                              <div class="timeline-item">
                                <span class="time"><i class="fa fa-clock"></i> <?= $row['created_at'] ?></span>
                                <h3 class="timeline-header">
                                  <?= ucfirst($row['communication_type']) ?>: <?= htmlspecialchars($row['subject']) ?>
                                  <span class="pull-right">
                                    <a href="../communications/edit.php?id=<?= $row['id'] ?>" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i></a>
                                    <a href="../communications/delete.php?id=<?= $row['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete this entry?')"><i class="fa fa-trash"></i></a>
                                  </span>
                                </h3>
                                <div class="timeline-body"><?= nl2br(htmlspecialchars($row['details'])) ?></div>
                              </div>
                            </li>
                          <?php endwhile; ?>
                        </ul>
                      <?php else: ?>
                        <p class="text-muted">No communications recorded.</p>
                      <?php endif; ?>
                    </div>
                  </div>

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
