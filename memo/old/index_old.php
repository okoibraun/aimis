<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../config/db.php');

// if (!isset($_SESSION['user_id'])) {
//     header('Location: login.php');
//     exit();
// }


// Memo count
if($_SESSION['user_role'] == 'staff') {
    $memo_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM memos WHERE target_user_id = {$_SESSION['user_id']}"))['total'];

} else if($_SESSION['user_role'] == 'admin') {
    $memo_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM memos WHERE department = '{$_SESSION['user_department']}' AND target_user_id = {$_SESSION['user_id']}"))['total'];
} else {
    $memo_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM memos"))['total'];
}

// User count
$user_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];

// Active folders
$folder_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM folders"))['total'];

// Total Comments
$comment_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM memo_comments"))['total'];

// #$query = "SELECT * FROM memos ORDER BY created_at DESC";
// $result = mysqli_query($conn, $query);
$where = [];

if (!empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where[] = "(m.title LIKE '%$search%' OR u.name LIKE '%$search%')";
}

if (!empty($_GET['date_from'])) {
    $date_from = mysqli_real_escape_string($conn, $_GET['date_from']);
    $where[] = "DATE(m.created_at) >= '$date_from'";
}

if (!empty($_GET['date_to'])) {
    $date_to = mysqli_real_escape_string($conn, $_GET['date_to']);
    $where[] = "DATE(m.created_at) <= '$date_to'";
}

if (!empty($_GET['tag'])) {
    $tag_id = intval($_GET['tag']);
    $where[] = "EXISTS (SELECT 1 FROM memo_tags mt WHERE mt.memo_id = m.id AND mt.tag_id = $tag_id)";
}

if (!empty($_GET['folder_id'])) {
    $folder_id = intval($_GET['folder_id']);
    $where[] = "m.folder_id = '$folder_id'";
}

$access_type = (isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'public');
// Apply access control based on user role
if ($access_type == 'staff') {
    // $where[] = "m.recipient = '{$_SESSION['user_id']}' OR m.forward_to = '{$_SESSION['user_id']}'"; // Show only private memos assigned to the user
    // $where[] = "m.sender = '{$_SESSION['user_id']}'";
    // $where[] = "m.forward_to = '{$_SESSION['user_id']}'";
    $where[] = "m.department = '{$_SESSION['user_department']}'"; // Show only team memos for the user's department
    $where[] = "m.company_id = '{$_SESSION['company_id']}' OR m.sender = '{$_SESSION['user_id']}' OR m.recipient = '{$_SESSION['user_id']}' OR m.forward_to = '{$_SESSION['user_id']}'";
} elseif ($access_type == 'admin') {
    // $where[] = "m.recipient = '{$_SESSION['user_id']}' OR m.forward_to = '{$_SESSION['user_id']}'"; // Show only private memos assigned to the user
    // $where[] = "m.sender = '{$_SESSION['user_id']}'";
    // $where[] = "m.forward_to = '{$_SESSION['user_id']}'";
    $where[] = "m.company_id = '{$_SESSION['company_id']}' OR m.sender = '{$_SESSION['user_id']}' OR m.recipient = '{$_SESSION['user_id']}' OR m.forward_to = '{$_SESSION['user_id']}'";
} elseif ($access_type == 'superadmin') {
    // No restriction for public memos
    // $where[] = "m.access_type = 'public'";
    $where[] = "m.company_id = '{$_SESSION['company_id']}'";
}

$where_sql = count($where) > 0 ? "WHERE " . implode(' AND ', $where) : "";

$query = "SELECT m.*, u.name AS author_name
          FROM memos m
          LEFT JOIN users u ON m.created_by = u.id
          $where_sql
          ORDER BY m.created_at DESC";

$result = mysqli_query($conn, $query);
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Memos Dashboard</title>
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
        <!--begin::App Content Header-->
        <div class="app-content-header">
            <!--begin::Container-->
            <div class="container-fluid">
                <!--begin::Row-->
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Memos Dashboard</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Memos</li>
                        </ol>
                    </div>
                </div>
                <!--end::Row-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <!--begin::Col-->
                <div class="col-lg-3 col-6">
                    <!--begin::Small Box Widget 1-->
                    <div class="small-box text-bg-primary">
                        <div class="inner">
                            <h3><?php echo $memo_count; ?></h3>
                            <p>Memos</p>
                        </div>
                        <span class="small-box-icon">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <a href="#" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            More info <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                    <!--end::Small Box Widget 1-->
                </div>
                <!--end::Col-->
                <div class="col-lg-3 col-6">
                    <!--begin::Small Box Widget 3-->
                    <div class="small-box text-bg-warning">
                        <div class="inner">
                            <h3><?php echo $folder_count; ?></h3>
                            <p>Folders</p>
                        </div>
                        <div class="small-box-icon"><i class="bi bi-folder"></i></div>
                        <a href="#" class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                            More info <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                    <!--end::Small Box Widget 3-->
                </div>
                <!--end::Col-->
                <div class="col-lg-3 col-6">
                    <!--begin::Small Box Widget 4-->
                    <div class="small-box text-bg-danger">
                        <div class="inner">
                            <h3><?php echo $comment_count; ?></h3>
                            <p>Comments</p>
                        </div>
                        <span class="small-box-icon"><i class="bi bi-chat-left-dots"></i></span>
                        <a href="#" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            More info <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                    <!--end::Small Box Widget 4-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->

            <!-- begin row -->
            <div class="row">
                <div class="col-lg-12">
                    <?php if(isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <?php if(isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- end row -->
             
            <!--begin::Row-->
            <div class="row">
                <!-- Start col -->
                <div class="col-lg-12">
                    <div class="card">

                        <div class="card-header">
                            <div class="row">
                                <div class="col-lg-6">
                                    <h4>Memos</h4>
                                </div>
                                <div class="col-lg-6 text-end">
                                    <a href="create.php" class="btn btn-primary">Send New Memo</a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Access</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($result)) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                                            <td><?php echo ucfirst($row['status']); ?></td>
                                            <td><?php echo ucfirst($row['access_type']); ?></td>
                                            <td><?php echo $row['created_at']; ?></td>
                                            <td>
                                                <a href="memo.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">View</a>
                                                <!--<a href="edit.php?id=<?php //echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                                <a href="delete.php?id=<?php //echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this memo?')">Delete</a> -->
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.card -->
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
