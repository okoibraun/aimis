<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../config/db.php');
include("../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

// Check User Permissions
$page = "audit";
$user_permissions = get_user_permissions($_SESSION['user_id']);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

// (Optionally) Restrict to admins only:
// if ($_SESSION['user_role'] !== 'admin' || $_SESSION['user_role'] !== 'superadmin') {
//     // If the user is not an admin or superadmin, deny access
//     echo "Access denied.";
//     exit;
// }

// Fetch all read events
$query = "SELECT m.title, u.name AS reader, mr.read_at, mr.replied_at, mr.forwarded_at
          FROM memo_reads mr
          JOIN memos m ON mr.memo_id = m.id
          JOIN users u ON mr.user_id = u.id
          WHERE mr.company_id = $company_id
          ORDER BY mr.read_at DESC";
$result = mysqli_query($conn, $query);
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Memo - Audit</title>
    <?php include_once("../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row mt-5">
                <!-- Start col -->
                <div class="col-lg-12 connectedSortable">
                  
                    <div class="card shadow">
                        <div class="card-header">
                            <h4 class="mb-0">Memo Audit Trail</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered DataTable">
                                <thead>
                                    <tr>
                                        <th>Memo Title</th>
                                        <th>User</th>
                                        <th>Read At</th>
                                        <th>Replied At</th>
                                        <th>Forwarded At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                                        <td><?php echo htmlspecialchars($row['reader']); ?></td>
                                        <td><?php echo $row['read_at']; ?></td>
                                        <td><?= $row['replied_at']; ?></td>
                                        <td><?= $row['forwarded_at']; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <!-- /.Start col -->
            </div>
            <!-- /.row (main row) -->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>