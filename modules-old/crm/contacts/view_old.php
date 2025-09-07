<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

$company_id = get_current_company_id();
$id = intval($_GET['id']);

$stmt = $conn->prepare("
    SELECT c.*, co.name AS company_name 
    FROM crm_contacts c 
    LEFT JOIN crm_companies co ON c.company_id = co.id 
    WHERE c.id = ? AND c.company_id = ?
");
$stmt->bind_param("ii", $id, $company_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Contact not found.");
}
$contact = $result->fetch_assoc();

// Get communications
$stmt2 = $conn->prepare("
    SELECT * FROM crm_communications 
    WHERE contact_id = ? AND company_id = ?
    ORDER BY date DESC
");
$stmt2->bind_param("ii", $id, $company_id);
$stmt2->execute();
$comms = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

$related_type = 'contact';
$related_id = $contact['id'];

$stmt = $conn->prepare("SELECT * FROM crm_communications WHERE company_id = ? AND related_type = ? AND related_id = ? ORDER BY created_at DESC");
$stmt->bind_param("isi", $company_id, $related_type, $related_id);
$stmt->execute();
$comm_result = $stmt->get_result();

// For Sales Integration from CRM (Invoices and Opportunities)
// Fetch linked customer
$customer_res = $conn->query("SELECT id FROM sales_customers WHERE contact_id = $id");
$customer = $customer_res->fetch_assoc();

if ($customer) {
    $cust_id = $customer['id'];

    // Fetch invoices
    $invoices_res = $conn->query("SELECT * FROM sales_invoices WHERE customer_id = $cust_id ORDER BY issued_date DESC");

    // Fetch opportunities
    $deals_res = $conn->query("SELECT * FROM crm_deals WHERE contact_id = $id ORDER BY created_at DESC");
}

// // Marketing Integration
// $marketing_res = $conn->query("
//   SELECT c.id, c.name, mc.status, mc.sent_at
//   FROM marketing_campaign_contacts mcc
//   JOIN marketing_campaigns c ON mcc.campaign_id = c.id
//   LEFT JOIN marketing_campaign_status mc ON mc.campaign_id = c.id AND mc.contact_id = $id
//   WHERE mcc.contact_id = $id
// ");


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
            <section class="content-header">
              <h1>Contact Details</h1>
              <a href="edit.php?id=<?= $contact['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
              <a href="../ai/view.php?id=<?= $contact['id'] ?>" class="btn btn-warning btn-sm">View AI Analysis Score</a>
              <a href="list.php" class="btn btn-default btn-sm">Back to List</a>
            </section>

            <section class="content">
              <div class="row">
                <div class="col-md-4">
                  <div class="card ">
                    <div class="card-header with-border"><h3 class="card-title">Profile</h3></div>
                    <div class="card-body">
                      <strong>Name:</strong><br><?= htmlspecialchars($contact['full_name']) ?><br><br>
                      <strong>Email:</strong><br><?= htmlspecialchars($contact['email']) ?><br><br>
                      <strong>Phone:</strong><br><?= htmlspecialchars($contact['phone']) ?><br><br>
                      <strong>Job Title:</strong><br><?= htmlspecialchars($contact['job_title']) ?><br><br>
                      <strong>Company:</strong><br><?= htmlspecialchars($contact['company_name']) ?><br><br>
                      <strong>Notes:</strong><br><?= nl2br(htmlspecialchars($contact['notes'])) ?><br>
                    </div>
                  </div>
                </div>

                <div class="col-md-8">
                  <div class="card card-info">
                    <div class="card-header with-border"><h3 class="card-title">Communication History</h3></div>
                    <div class="card-body">
                      <?php if (count($comms) === 0): ?>
                        <p class="text-muted">No communication records yet.</p>
                      <?php else: ?>
                        <ul class="timeline">
                          <?php foreach ($comms as $comm): ?>
                            <li>
                              <i class="fa fa-<?= $comm['type'] === 'call' ? 'phone' : ($comm['type'] === 'email' ? 'envelope' : 'calendar') ?> bg-blue"></i>
                              <div class="timeline-item">
                                <span class="time"><i class="fa fa-clock-o"></i> <?= date('Y-m-d H:i', strtotime($comm['date'])) ?></span>
                                <h3 class="timeline-header">
                                  <?= ucfirst($comm['type']) ?> with <?= htmlspecialchars($comm['with_whom']) ?>
                                </h3>
                                <div class="timeline-body">
                                  <?= nl2br(htmlspecialchars($comm['summary'])) ?>
                                </div>
                              </div>
                            </li>
                          <?php endforeach; ?>
                          <li><i class="fa fa-clock-o bg-gray"></i></li>
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

                  <div class="card card-info">
                    <div class="card-header"><h3 class="card-title">Sales Activity</h3></div>
                    <div class="card-body">
                      <h4>Invoices</h4>
                      <ul>
                        <?php while($inv = $invoices_res->fetch_assoc()): ?>
                          <li>
                            <a href="../../../modules/sales/invoices/view.php?id=<?= $inv['id'] ?>">
                              <?= $inv['invoice_number'] ?> - <?= $inv['status'] ?> - <?= $inv['total_amount'] ?>
                            </a>
                          </li>
                        <?php endwhile; ?>
                      </ul>

                      <h4>Deals</h4>
                      <ul>
                        <?php while($deal = $deals_res->fetch_assoc()): ?>
                          <li>
                            <strong><?= $deal['title'] ?></strong> – <?= $deal['stage'] ?> – <?= $deal['value'] ?>
                          </li>
                        <?php endwhile; ?>
                      </ul>
                    </div>
                  </div>

                  <!-- <div class="card card-warning">
                    <div class="card-header"><h3 class="card-title">Marketing Campaigns</h3></div>
                    <div class="card-body">
                      <ul>
                        <?php //while($mkt = $marketing_res->fetch_assoc()): ?>
                          <li>
                            <strong><?= //$mkt['name'] ?></strong> – <?= //$mkt['status'] ?? 'Pending' ?> –
                            <?= //$mkt['sent_at'] ?? 'N/A' ?>
                          </li>
                        <?php //endwhile; ?>
                      </ul>
                    </div>
                  </div> -->


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
