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
                        <h3 class="mb-0">Memo</h3>
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
                <!-- Start col -->
                <div class="col-lg-12 connectedSortable">
                    <!-- begin filter section -->
                    <!-- / end filter section -->

                    <!--begin::Container-->
                    <div class="container-fluid">
                        <!--begin::Row-->
                        <div class="row">
                            <div class="col-sm-6">
                                <h2 class="mb-0">Memo Details</h2>
                            </div>
                            <div class="col-sm-6">
                                <div class="float-sm-end">
                                    <a href="index.php" class="btn btn-secondary mb-3">Back to List</a>
                                    <a href="create.php" class="btn btn-primary mb-3">Create New Memo</a>
                                    <a href="#" class="btn btn-primary mb-3"><i class="fas fa-reply"></i> Reply</a>
                                    <a href="#" class="btn btn-primary mb-3"><i class="fas fa-forward"></i> Forward</a>
                                    <!-- <a href="edit.php?id=<?php //echo $id; ?>" class="btn btn-primary mb-3">Edit</a>
                                    <a href="delete.php?id=<?php //echo $id; ?>" class="btn btn-danger mb-3">Delete</a> -->
                                </div>
                            </div>
                        </div>
                        <!--end::Row-->
                    </div>
                    <!--end::Container-->
                <!-- /.card -->
                </div>
                <!-- /.Start col -->
            </div>
            <hr />

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
                        
                        <div class="card-footer">
                        </div>
                    </div>

                    <!-- comments -->
                    <div class="mt-5">
                        <h4 class="mb-4">Comments</h4>

                        <?php while($c = mysqli_fetch_assoc($comments)): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div>
                                        <strong><?php echo htmlspecialchars($c['user_name']); ?>:</strong>
                                        <small class="text-muted float-end"><?php echo $c['created_at']; ?></small>
                                    </div>
                                    <p><?php echo nl2br(htmlspecialchars($c['comment'])); ?></p>
                                </div>
                            </div>
                        <?php endwhile; ?>

                        <div class="card">
                            <div class="card-body">
                                <form method="post" action="comment.php">
                                    <input type="hidden" name="memo_id" value="<?php echo $memo['id']; ?>">
                                    <textarea name="comment" class="form-control mb-2" placeholder="Write your comment..." required></textarea>
                                    <button type="submit" class="btn btn-primary float-end">Post Comment</button>
                                </form>
                            </div>
                        </div>
                    </div>
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
                                <strong>Created By:</strong> <?php echo htmlspecialchars($memo['created_by']); ?>
                                <strong>Created At:</strong> <?php echo date('Y-m-d H:i:s', strtotime($memo['created_at'])); ?>
                                <hr>
                                <strong>Updated By:</strong> <?php echo $memo['updated_by']; ?>
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
                                <a href="#version_history.php?memo_id=<?php echo $id; ?>" class="btn btn-primary">View All Versions</a>
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
