<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}


$id = (isset($_GET['id']) ? intval($_GET['id']) : 0);

// Fetch memo details
$query = "SELECT * FROM memos WHERE id = $id LIMIT 1";
$result = mysqli_query($conn, $query);
$memo = mysqli_fetch_assoc($result);

if (!$memo) {
    echo "Memo not found.";
    //exit();
}

// Track reading (audit trail)
$user_id = $_SESSION['user_id']; // Replace with session user id
mysqli_query($conn, "INSERT INTO memo_reads (memo_id, user_id, read_at) VALUES ($id, $user_id, NOW())");

// Mark as read in audit table
$read_at = date('Y-m-d H:i:s');
mysqli_query($conn, "INSERT INTO memo_reads (memo_id, user_id, read_at) VALUES ('$id', '$user_id', '$read_at')");

// Optional: Audit Log
include_once('../includes/audit_log.php');
log_audit($conn, $user_id, 'Viewed', "Read Memo ID {$id}");

// Fetch Comments
$comments = mysqli_query($conn, "
    SELECT memo_comments.*, users.name AS user_name 
    FROM memo_comments 
    JOIN users ON memo_comments.user_id = users.id 
    WHERE memo_comments.memo_id = $id 
    ORDER BY memo_comments.created_at ASC
");

// Check if liked
$liked = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM memo_likes 
    WHERE memo_id = $id AND user_id = ".$_SESSION['user_id']."
"));
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | View Memo</title>
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
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
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
                                <div class="col-sm-6">
                                    <h3 class="mb-0">Memo Details</h3>
                                </div>
                                <div class="col-sm-6">
                                    <div class="float-sm-end">
                                        <a href="index.php" class="btn btn-secondary">Back to List</a>
                                        <a href="create.php" class="btn btn-primary">Create New Memo</a>
                                        <a href="reply_memo.php?memid=<?= $memo['id'] ?>" class="btn btn-primary"><i class="fas fa-reply"></i> Reply</a>
                                        <a href="forward_memo.php?memid=<?= $memo['id'] ?>" class="btn btn-primary"><i class="fas fa-forward"></i> Forward</a>
                                        <!-- <a href="edit.php?id=<?php //echo $id; ?>" class="btn btn-primary mb-3">Edit</a>
                                        <a href="delete.php?id=<?php //echo $id; ?>" class="btn btn-danger mb-3">Delete</a> -->
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
                            <h3 class="card-title">
                                <strong><?php echo htmlspecialchars($memo['title']); ?></strong>
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
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
                            </div>
                            <div class="card-body">
                                <strong>Status:</strong> <?php echo htmlspecialchars($memo['status']); ?>
                                <hr>
                                <strong>Created By:</strong>
                                <?php $created_by = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM users WHERE id={$memo['created_by']}")); ?>
                                <?php echo $created_by['name']; ?><br>
                                <strong>Created At:</strong> <?php echo date('Y-m-d H:i:s', strtotime($memo['created_at'])); ?>
                                <hr>

                                <strong>Updated By:</strong>
                                <?php $updated_by = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM users WHERE id={$memo['updated_by']}")); ?>
                                <?php echo $updated_by['name'] ?? ''; ?><br>
                                <strong>Updated At:</strong> <?php echo $memo['updated_at']; ?>
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
                            <div class="card-footer">
                                <a href="upload_attachment.php?memo_id=<?php echo $id; ?>" class="btn btn-primary">Upload Attachment</a>
                            </div>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3 class="card-title">Tags</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                    $tag_query = "SELECT t.name FROM tags t 
                                                INNER JOIN memo_tags mt ON t.id = mt.tag_id 
                                                WHERE mt.memo_id = $id";
                                    $tag_result = mysqli_query($conn, $tag_query);
                                    
                                    if (mysqli_num_rows($tag_result) > 0) {
                                        while ($tag = mysqli_fetch_assoc($tag_result)) {
                                            echo "<span class='badge-secondary'>{$tag['name']}</span> ";
                                        }
                                    } else {
                                        echo "No tags.";
                                    }    
                                ?>
                            </div>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-header">
                                <h3 class="card-title">Version History</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                    $version_query = "SELECT * FROM memo_versions WHERE memo_id = $id ORDER BY edited_at DESC LIMIT 5";
                                    // Fetch version history
                                    $version_result = mysqli_query($conn, $version_query);


                                    if (mysqli_num_rows($version_result) > 0) {
                                        while ($version = mysqli_fetch_assoc($version_result)) {
                                            $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM users WHERE id={$version['edited_by']}"))['name'];
                                            // echo $user;
                                            echo "<p><a href='memo_version.php?id={$version['id']}&memo_id={$id}'><strong>Edited By {$user}</strong> - {$version['edited_at']}</a></p>";
                                        }
                                    } else {
                                        echo "No version history.";
                                    }
                                ?>
                            </div>
                            <div class="card-footer">
                                <a href="version_history.php?memo_id=<?php echo $id; ?>" class="btn btn-primary">View All Versions</a>
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
