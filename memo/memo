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
$page = "view" ?? "memo";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

// Get memo ID from query parameters
// Validate the ID to ensure it's a valid integer
// If the ID is not valid, redirect to the index page with an error message.
$id = (isset($_GET['id']) ? intval($_GET['id']) : 0);
if ($id <= 0) {
    $_SESSION['message'] = "Invalid memo ID.";
    header('Location: ./');
    exit();
}

// Fetch memo details
$memo = $conn->query("SELECT * FROM memos WHERE id = $id LIMIT 1")->fetch_assoc();

// Check if memo exists
// If the memo does not exist, redirect to the index page with an error message.
if(!$memo) {
    $_SESSION['message'] = "Memo not found.";
    header('Location: ./');
    exit();
}

// Update memo read status
$conn->query("UPDATE memos SET status = 'read', is_read = 1 WHERE id = $id AND status = 'new'");
// Note: The above query updates the memo status to 'read' and sets is_read to 1 if it was previously 'new'.

// This assumes you have a table `memo_reads` to track which user has read which memo.
// Log reading (memo audit trail)
$conn->query("INSERT INTO memo_reads (company_id, employee_id, memo_id, user_id, read_at) VALUES ($company_id, $employee_id, $id, $user_id, NOW())");

// Log Activity
include_once('../functions/log_functions.php');
log_activity($user_id, $company_id, 'read_memo', "Read Memo: {$memo['title']}");

?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | View Memos</title>
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
                        <h2 class="mb-0">Memos</h2>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="./">Home</a></li>
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
            <div class="row mb-4">
                <!-- Start col -->
                <div class="col-lg-12">
                    <!-- begin filter section -->
                    <!-- / end filter section -->

                    <!-- card -->
                    <div class="card">
                        <div class="card-header">

                            <!--begin::Row-->
                            <div class="row">
                                <div class="col-sm-7">
                                    <h4 class="mb-0">Memo: <?php echo htmlspecialchars($memo['title']); ?></h4>
                                </div>
                                <div class="col-sm-5">
                                    <div class="float-sm-end">
                                        <a href="./" class="btn btn-secondary btn-sm">Back</a>
                                        <a href="reply_memo?memid=<?= $memo['id'] ?>" class="btn btn-primary btn-sm">
                                            <i class="bi bi-reply"></i> Reply
                                        </a>
                                        <a href="forward_memo?memid=<?= $memo['id'] ?>" class="btn btn-primary btn-sm">
                                            Forward <i class="bi bi-forward"></i>
                                        </a>
                                        <a href="send" class="btn btn-primary btn-sm">Send New Memo <i class="bi bi-send"></i></a>

                                    </div>
                                </div>
                            </div>
                            <!--end::Row-->
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.Start col -->
            </div>

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

            <div class="row">
                <!-- col-lg-8 -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            
                            <div class="row">
                                <div class="col-lg-6">
                                </div>
                                <div class="col-lg-6 text-end">
                                    <div class="card-tools">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                Action
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="reply_memo?memid=<?= $memo['id'] ?>">
                                                        <i class="bi bi-reply"></i>
                                                        Reply
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item" href="forward_memo?memid=<?= $memo['id'] ?>">
                                                        Forward
                                                        <i class="bi bi-forward"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="content">
                                <?php echo $memo['content']; ?>
                            </div>
                        </div>
                        
                    </div>

                    <!-- Reply -->
                    <?php
                    // Fetch replies
                    $replies = mysqli_query($conn, "SELECT * FROM memo_replies WHERE memo_id = $id ORDER BY date DESC");
                    $count_replies = mysqli_num_rows($replies);
                    if ($count_replies > 0) {
                        echo "<h4 class='mt-4'>Replies ({$count_replies})</h4>";
                        foreach ($replies as $reply) {
                    // else {
                    //     echo "<h4 class='mt-4'>No replies yet.</h4>";
                    // }
                    ?>
                    <div class="card card-default">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-user fa-2x"></i>
                                <?php //echo $thread['name']; ?> 
                                [ <?php
                                    $replieduser = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM users WHERE id={$reply['user_id']}"));
                                    echo $replieduser['name'];
                                ?> ] - Replied
                            </h5>
                            <span class="float-end"><?php echo $reply['date']; ?></span>
                        </div>
                        <div class="card-body">
                            <?php echo $reply['message']; ?>
                        </div>
                    </div>
                    <?php } } ?>

                    <!-- comments -->
                    
                    <!-- / comments -->
                </div>
                <!-- /.col-lg-8 -->

                 <!-- col-lg-4 -->
                <div class="col-lg-4">
                    <div class="row">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3 class="card-title">Meta</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                                        <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                        <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <strong>From:</strong>
                                <?php $created_by = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM users WHERE id={$memo['created_by']}")); ?>
                                <?php echo $created_by['name']; ?><br>
                                <strong>Created At:</strong> <?php echo date('Y-m-d H:i:s', strtotime($memo['created_at'])); ?>
                                <hr>
                            </div>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3 class="card-title">Tags</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                    $tags = $conn->query("SELECT t.name FROM tags t 
                                                INNER JOIN memo_tags mt ON t.id = mt.tag_id 
                                                WHERE mt.memo_id = $id");
                                    
                                    if ($tags->num_rows > 0) {
                                        foreach($tags as $tag) { ?>
                                            <span title="New Messages" class="badge text-bg-primary"><?= $tag['name'] ?></span>
                                        <?php }
                                    } else {
                                        echo "No tags.";
                                    }    
                                ?>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <h3 class="card-title">Attachments</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                    $attachment_query = "SELECT * FROM memo_attachments WHERE memo_id = $id";
                                    $attachment_result = mysqli_query($conn, $attachment_query);

                                    if (mysqli_num_rows($attachment_result) > 0) {
                                        while ($file = mysqli_fetch_assoc($attachment_result)) {
                                            echo "<a href='../uploads/memos/{$file['file_path']}' target='_blank'>{$file['filename']}</a><br>";
                                        }
                                    } else {
                                        echo "No attachments.";
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- / col-lg-4 -->
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
