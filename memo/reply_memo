<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../config/db.php');
include("../functions/role_functions.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

$memo_id = isset($_GET['memid']) ? intval($_GET['memid']) : 0;

$get_memo = mysqli_query($conn, "SELECT * FROM memos WHERE id = $memo_id");

$memo = mysqli_fetch_assoc($get_memo);
// if ($memo['target_user_id'] != $_SESSION['user_id']) {
//     $_SESSION['error'] = "You are not authorized to reply to this memo.";
//     // Redirect to index with an error message
//     header("Location: memo.php?id={$memo_id}");
//     exit();
// }

if (isset($_POST['submit'])) {
    $memo_id = isset($_POST['memo_id']) ? intval($_POST['memo_id']) : 0;
    $recipient = isset($_POST['recipient_id']) ? intval($_POST['recipient_id']) : 0;
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);


    $sql = "INSERT INTO memo_replies (memo_id, recipient_id, user_id, title, message) VALUES ($memo_id, $recipient, $user_id, '$title', '$message')";

    if (mysqli_query($conn, $sql)) {
        // Handle file upload if a file is provided
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $allowed_extensions = array('pdf', 'jpg', 'jpeg', 'png', 'gif', 'doc', 'docx');
            $file_name = $_FILES['file']['name'];
            $file_tmp = $_FILES['file']['tmp_name'];
            $file_size = $_FILES['file']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($file_ext, $allowed_extensions)) {
                $_SESSION['error'] = "Invalid file type. Allowed types: " . implode(", ", $allowed_extensions);
            } else if ($file_size > 10 * 1024 * 1024) { // 10MB limit
                $_SESSION['error'] = "File size exceeds limit of 10MB.";
            } else {
                $new_filename = time() . '_' . basename($file_name);
                $upload_path = '../uploads/memos/' . $new_filename;

                if (!is_dir('../uploads/memos/')) {
                    mkdir('../uploads/memos/', 0777, true);
                }

                if (move_uploaded_file($file_tmp, $upload_path)) {
                    // Insert attachment record
                    mysqli_query($conn, "INSERT INTO memo_attachments (memo_id, filename, file_path, uploaded_at) 
                                         VALUES ($memo_id, '$file_name', '$new_filename', NOW())");
                } else {
                    $_SESSION['error'] = "Upload failed.";
                }
            }
        }

        // Log reading (memo audit trail)
        $conn->query("INSERT INTO memo_reads (company_id, employee_id, memo_id, user_id, replied_at) VALUES ($company_id, $employee_id, $memo_id, $user_id, NOW())");

        // Log Audit
        include_once('../functions/log_functions.php');
        log_activity($user_id, $company_id, 'replied_memo', "Replied Memo: {$title} to {$recipient}");

        //Notify users about the new memo creation
        include_once('../includes/notify.php');

        // Notify all users except the uploader
        $users = mysqli_query($conn, "SELECT id FROM users WHERE id != ".$_SESSION['user_id']);
        while ($u = mysqli_fetch_assoc($users)) {
            notify_user($conn, $u['id'], "Memo replied: " . $memo_title);
        }

        $_SESSION['success'] = "Memo reply has been successfully sent.";
        header("Location: memo?id={$memo_id}");
        exit();
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }

}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Memos</title>
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
            <div class="row">
                <!-- Start col -->
                <div class="col-lg-12 connectedSortable">

                    <div class="container mt-5">
                        <div class="card">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="card-header">
                                    <h3 class="card-title">Reply Memo</h3>
                                    <div class="card-tools">
                                        <a href="memo?id=<?= $memo['id'] ?>" class="btn btn-secondary btn-sm">X</a>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Title:</label>
                                        <input type="text" value="<?= $memo['title'] ?>" class="form-control" readonly>
                                        <input type="hidden" name="title" value="RE: <?= $memo['title'] ?>" class="form-control">
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Recipient:</label>
                                                <input type="text" value="<?php $user = $db->query("SELECT name FROM users WHERE id='{$memo['created_by']}'")->fetch_assoc(); echo $user['name']; ?>" class="form-control" readonly>
                                                <input type="hidden" name="recipient_id" value="<?= $memo['created_by'] ?>">
                                                <input type="hidden" name="memo_id" value="<?= $memo['id'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Date Created:</label>
                                                <input type="text" class="form-control text-mute" value="<?= $memo['created_at'] ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
        
                                    <div class="form-group">
                                        <label>Message:</label>
                                        <textarea name="message" id="summernote" class="form-control" rows="10"></textarea>
                                    </div>

                                    <div class="form-group mt-2">
                                        <label for="folder_id">Attachment <small>Select File:</small></label>
                                        <label><small>(pdf, jpg, jpeg, png, gif, doc, docx)</small></label>
                                        <input type="file" name="file" class="form-control" placeholder="pdf, jpg, jpeg, png, gif, doc, docx">
                                    </div>
                                </div>

                                <div class="card-footer text-end">
                                    <a href="memo?id=<?= $memo['id'] ?>" class="btn btn-danger">Cancel</a>
                                    <button type="submit" name="submit" class="btn btn-success">Reply</button>
                                </div>
    
                            </form>
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
    <script>
        $(document).ready(function() {
            $('#summernote').summernote({
                height: 300
            });
        });
    </script>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>