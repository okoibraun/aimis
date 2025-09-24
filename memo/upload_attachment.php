<?php
session_start();
// Include database connection and header
include('../config/db.php');
include("../functions/role_functions.php");

if (!isset($_GET['memo_id'])) {
    header('Location: ./');
    exit();
}

$memo_id = intval($_GET['memo_id']);
$message = "";

if (isset($_POST['upload'])) {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        
        $allowed_extensions = array('pdf', 'jpg', 'jpeg', 'png', 'gif', 'doc', 'docx');
        $file_name = $_FILES['file']['name'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_size = $_FILES['file']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_extensions)) {
            // echo "Invalid file type.";
            // exit();
            $message = "Invalid file type. Allowed types: " . implode(", ", $allowed_extensions);
        } else if ($file_size > 10 * 1024 * 1024) { // 10MB limit
            // echo "File size exceeds limit.";
            // exit();
            $message = "File size exceeds limit of 10MB.";
        } else {

            $new_filename = time() . '_' . basename($file_name);
            $upload_path = '../uploads/memos/' . $new_filename;
    
            if (!is_dir('../uploads/memos/')) {
                mkdir('../uploads/memos/', 0777, true);
            }
    
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $sql = "INSERT INTO memo_attachments (memo_id, filename, file_path, uploaded_at) 
                        VALUES ('$memo_id', '$file_name', '$new_filename', NOW())";
    
                if (mysqli_query($conn, $sql)) {
                    // Log Activity
                    include_once('../functions/log_functions.php');
                    log_activity($user_id, $company_id, 'upload_file', "Uploaded File: {$new_filename} to Memo {$memo_id}");

                    header('Location: memo?id='.$memo_id.'&msg=FileUploaded');
                    exit();
                } else {
                    echo "Database error: " . mysqli_error($conn);
                }
            } else {
                // echo "Upload failed.";
                $message = "Upload failed.";
            }
        }

    } else {
        // echo "No file selected.";
        $message = "No file selected.";
    }
}

?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Upload Attachment</title>
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
                        <h2>Upload Attachment</h2>
                        <?php if ($message): ?>
                            <div class="alert alert-danger"><?php echo $message; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Select File: <small>(pdf, jpg, jpeg, png, gif, doc, docx)</small></label>
                                <input type="file" name="file" class="form-control" placeholder="pdf, jpg, jpeg, png, gif, doc, docx" required>
                            </div>

                            <button type="submit" name="upload" class="btn btn-primary">Upload</button>
                        </form>

                        <hr>
                        <a href="memo?id=<?php echo $memo_id; ?>" class="btn btn-secondary">Back to Memo</a>
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