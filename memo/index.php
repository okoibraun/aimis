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
$page = "list" ?? "index" ?? "manage";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$super_roles = super_roles();

if (!in_array($_SESSION['role'], $super_roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$company_id = $_SESSION['company_id'];
$user_id = $_SESSION['user_id'];
$view = (isset($_GET['view'])) ? $_GET['view'] : "";
$user_role = $_SESSION['user_role'];

// Memo count
if($user_role == 'staff') {
    $memo_count = $conn->query("SELECT COUNT(*) as total FROM memos WHERE company_id = $company_id AND recipient = {$_SESSION['user_id']}")->fetch_assoc()['total'];

} else if(in_array($user_role, super_roles())) {
    $memo_count = $conn->query("SELECT COUNT(*) as total FROM memos WHERE company_id = $company_id")->fetch_assoc()['total'];
} else {
    $memo_count = $conn->query("SELECT COUNT(*) as total FROM memos")->fetch_assoc()['total'];
}

// Active folders
$folder_count = $conn->query("SELECT COUNT(*) as total FROM folders")->fetch_assoc()['total'];
if($user_role == 'staff') {
    $folder_count = $conn->query("SELECT COUNT(*) as total FROM folders WHERE company_id = $company_id AND created_by = $user_id")->fetch_assoc()['total'];
} else if(in_array($user_role, super_roles())) {
    $folder_count = $conn->query("SELECT COUNT(*) as total FROM folders WHERE company_id = $company_id")->fetch_assoc()['total'];
} else {
   $folder_count = $conn->query("SELECT COUNT(*) as total FROM folders")->fetch_assoc()['total'];
}

// #$query = "SELECT * FROM memos ORDER BY created_at DESC";
// $result = mysqli_query($conn, $query);
$where = [];

// if (!empty($_GET['search'])) {
//     $search = mysqli_real_escape_string($conn, $_GET['search']);
//     $where[] = "(m.title LIKE '%$search%' OR u.name LIKE '%$search%')";
// }

// if (!empty($_GET['date_from'])) {
//     $date_from = mysqli_real_escape_string($conn, $_GET['date_from']);
//     $where[] = "DATE(m.created_at) >= '$date_from'";
// }

// if (!empty($_GET['date_to'])) {
//     $date_to = mysqli_real_escape_string($conn, $_GET['date_to']);
//     $where[] = "DATE(m.created_at) <= '$date_to'";
// }

// if (!empty($_GET['tag'])) {
//     $tag_id = intval($_GET['tag']);
//     $where[] = "EXISTS (SELECT 1 FROM memo_tags mt WHERE mt.memo_id = m.id AND mt.tag_id = $tag_id)";
// }

// if (!empty($_GET['folder_id'])) {
//     $folder_id = intval($_GET['folder_id']);
//     $where[] = "m.folder_id = '$folder_id'";
// }

$access_type = (isset($user_role) ? $user_role : 'public');

// Apply access control based on user role
// if ($access_type == 'hr') {
if(in_array($user_role, departmental_roles())) {
    $where[] = "m.department = '{$_SESSION['user_department']}'"; // Show only team memos for the user's department
    if(isset($view) && $view == 'inbox') {
        $where[] = "m.status = 'new' OR m.status = 'read'";
        $where[] = "m.company_id = $company_id AND m.recipient = $user_id";
    } elseif(isset($view) && $view == 'draft') {
        $where[] = "m.created_by = $user_id AND m.status = 'draft'";
        $where[] = "m.company_id = $company_id";
    } elseif(isset($view) && $view == 'sent') {
        $where[] = "m.sender = $user_id";
        $where[] = "m.company_id = $company_id";
    } elseif(isset($view) && $view == 'archived') {
        $where[] = "m.company_id = $company_id AND m.sender = $user_id AND m.status = 'archived' OR m.recipient = $user_id OR m.forward_to = $user_id";
    } elseif(isset($view) && $view == 'forwarded') {
        $where[] = "m.status = 'forwarded'";
        $where[] = "m.company_id = $company_id OR m.forward_to = $user_id";
    } else {
        //$where[] = "m.target_user_id = $user_id";
        $where[] = "m.company_id = $company_id OR m.sender = $user_id OR m.recipient = $user_id OR m.forward_to = $user_id";
    }
} elseif ($access_type == 'admin') {
    if(isset($view) && $view == 'inbox') {
        $where[] = "m.status = 'new' OR m.status = 'read'";
        $where[] = "m.company_id = $company_id AND m.recipient = $user_id";
    } elseif(isset($view) && $view == 'draft') {
        $where[] = "m.created_by = $user_id AND m.status = 'draft'";
        $where[] = "m.company_id = $company_id";
    } elseif(isset($view) && $view == 'sent') {
        $where[] = "m.sender = $user_id";
        $where[] = "m.company_id = $company_id";
    } elseif(isset($view) && $view == 'archived') {
        $where[] = "m.status = 'archived'";
        $where[] = "m.company_id = $company_id AND m.sender = $user_id OR m.recipient = $user_id OR m.forward_to = $user_id";
    } elseif(isset($view) && $view == 'forwarded') {
        $where[] = "m.status = 'forwarded'";
        $where[] = "m.company_id = $company_id OR m.forward_to = $user_id";
    } else {
        //$where[] = "m.target_user_id = $user_id";
        $where[] = "m.company_id = $company_id OR m.sender = $user_id OR m.recipient = $user_id OR m.forward_to = $user_id";
    }
} elseif ($access_type == 'superadmin') {
    // No restriction for public memos
    if(isset($view) && $view == 'inbox') {
        $where[] = "m.status = 'new' OR m.status = 'read'";
        $where[] = "m.company_id = $company_id AND m.recipient = $user_id";
    } elseif(isset($view) && $view == 'draft') {
        $where[] = "m.created_by = $user_id AND m.status = 'draft'";
        $where[] = "m.company_id = $company_id";
    } elseif(isset($view) && $view == 'sent') {
        $where[] = "m.sender = $user_id";
        $where[] = "m.company_id = $company_id";
    } elseif(isset($view) && $view == 'archived') {
        $where[] = "m.status = 'archived'";
        $where[] = "m.company_id = $company_id AND m.sender = $user_id OR m.recipient = $user_id OR m.forward_to = $user_id";
    } elseif(isset($view) && $view == 'forwarded') {
        $where[] = "m.status = 'forwarded'";
        $where[] = "m.company_id = $company_id OR m.forward_to = $user_id";
    } else {
        //$where[] = "m.target_user_id = $user_id";
        $where[] = "m.company_id = $company_id";
    }
}

$where_sql = count($where) > 0 ? "WHERE " . implode(' AND ', $where) : "";

$memos = $conn->query("SELECT m.*, u.name AS author_name
          FROM memos m
          LEFT JOIN users u ON m.created_by = u.id
          $where_sql
          ORDER BY m.created_at DESC");
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
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
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
                    <?php if(isset($_SESSION['message'])): ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
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
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            -- Goto --
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a href="./" class="dropdown-item">All</a></li>
                                            <li><a class="dropdown-item" href="?view=inbox">Inbox</a></li>
                                            <li><a class="dropdown-item" href="?view=draft">Draft</a></li>
                                            <li><a class="dropdown-item" href="?view=sent">Sent</a></li>
                                            <li><a class="dropdown-item" href="?view=forwarded">Forwarded</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="?view=archived">Archived</a></li>
                                        </ul>
                                    </div>
                                    <a href="send" class="btn btn-primary">Send New Memo</a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>&nbsp;</th>
                                        <th>From</th>
                                        <th>Subject</th>
                                        <th class="text-end">Received</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($memos as $memo) { ?>
                                        <tr class="align-items-center" style="align-items: center; cursor: pointer;" onclick="window.location.href='memo?id=<?php echo $memo['id']; ?>'">
                                            <td class="text-center">
                                                <div class="text-white float-end" style="height: 35px; width:35px; border-radius: 20px; background-color: #007bff; color: white; display: flex; align-items: center; justify-content: center;">
                                                    <?php $names = explode(" ", $memo['author_name']); ?>
                                                    <?php for($i=0; $i < count($names); $i++) { echo $names[$i][0]; } ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if(($memo['status'] == 'new' && $memo['recipient'] == $_SESSION['user_id']) || ($memo['is_forwarded'] == 1 && $memo['status'] == 'forwarded' && $memo['forward_to'] == $_SESSION['user_id'])) { ?>
                                                    <strong><?= $memo['author_name'] ?></strong>
                                                    <span title="New Messages" class="badge text-bg-primary"> new </span>
                                                <?php } else { ?>
                                                    <span class="text-muted"><?= $memo['author_name'] ?></span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                
                                                <?php if(($memo['status'] == 'new' && $memo['recipient'] == $_SESSION['user_id']) || ($memo['is_forwarded'] == 1 && $memo['status'] == 'forwarded' && $memo['forward_to'] == $_SESSION['user_id'])) { ?>
                                                    <strong><?php echo htmlspecialchars($memo['title']); ?></strong>
                                                <?php } else { ?>
                                                    <span class="text-muted"><?php echo htmlspecialchars($memo['title']); ?></span>
                                                <?php } ?>
                                            </td>
                                            <td class="text-end">
                                                <small>
                                                    <?php echo $memo['created_at']; ?>
                                                </small>
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
