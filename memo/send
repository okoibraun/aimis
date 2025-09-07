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
$page = "add" ?? "send";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

if (isset($_POST['sendMemo'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $access_type = $_POST['access_type'];
    $created_by = $_SESSION['user_id']; // Replace with actual logged-in user id
    // $status = $_POST['status'];
    $status = "new";
    $folder_id = !empty($_POST['folder_id']) ? intval($_POST['folder_id']) : "NULL";
    $recipient = isset($_POST['recipient']) ? intval($_POST['recipient']) : 0;
    $company_id = $_SESSION['company_id']; // Assuming company_id is stored in session
    $department = $_SESSION['user_role']; // Assuming user department is stored in session


    $sql = "INSERT INTO memos (sender, recipient, company_id, department, title, content, access_type, folder_id, created_by, created_at, status, target_user_id, is_sent) 
            VALUES ('$created_by', '$recipient', '$company_id', '$department', '$title', '$content', '$access_type', '$folder_id', '$created_by', NOW(), '$status', '$recipient', 1)";
    

    if (mysqli_query($conn, $sql)) {
        // get the last inserted memo ID
        $memo_id = $conn->insert_id;

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
        // Optionally, you can handle tags here if needed
        $tags = mysqli_real_escape_string($conn, $_POST['tags']);
        $tag_list = array_map('trim', explode(',', $tags));

        foreach ($tag_list as $tag_name) {
            if (!empty($tag_name)) {
                // Check if tag already exists
                $tag_check = mysqli_query($conn, "SELECT id FROM tags WHERE name = '$tag_name'");
                if (mysqli_num_rows($tag_check) > 0) {
                    $tag = mysqli_fetch_assoc($tag_check);
                    $tag_id = $tag['id'];
                } else {
                    // Insert new tag
                    mysqli_query($conn, "INSERT INTO tags (name) VALUES ('$tag_name')");
                    $tag_id = mysqli_insert_id($conn);
                }
                // Link memo and tag
                mysqli_query($conn, "INSERT INTO memo_tags (memo_id, tag_name, tag_id) VALUES ('$memo_id', '$tag_name', '$tag_id')");
            }
        }

        // Log reading (memo audit trail)
        $conn->query("INSERT INTO memo_reads (company_id, employee_id, memo_id, user_id, read_at) VALUES ($company_id, $employee_id, $memo_id, $user_id, NOW())");

        // Log Activity
        include_once('../functions/log_functions.php');
        log_activity($user_id, $company_id, 'sent_memo', "Sent Memo: {$title} to {$recipient}");

        //Notify users about the new memo creation
        // include_once('../includes/notify.php');

        // Notify all users except the uploader
        // $users = mysqli_query($conn, "SELECT id FROM users WHERE id != ".$_SESSION['user_id']);
        // while ($u = mysqli_fetch_assoc($users)) {
        //     notify_user($conn, $u['id'], "New memo uploaded: " . $memo_title);
        // }

        $_SESSION['success'] = "Memo Sent Successfully";
        header('Location: ./');
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
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
            
            <form method="post" enctype="multipart/form-data">
                <!--begin::Row-->
                <div class="row mt-5">
                    <!-- Start col -->
                    <div class="col-lg-9">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">New Memo</h3>
                                <div class="card-tools">
                                    <a href="./" class="btn btn-danger btn-sm">X</a>
                                    <button type="submit" name="sendMemo" class="btn btn-success">Send</button>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="form-group">
                                    <input type="text" name="title" class="form-control" placeholder="Title:" required>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <select name="recipient" class="form-control select2" required>
                                                <option value="">Recipient: </option>
                                                <?php $users = $conn->query("SELECT id, name FROM users WHERE company_id = '{$_SESSION['company_id']}' AND status = 'active' ORDER BY name ASC");
                                                    foreach($users as $user) { ?>
                                                    <?php if ($user['id'] == $_SESSION['user_id']) continue; // Skip current user ?>
                                                    <option value="<?php echo $user['id']; ?>">
                                                        <?php echo htmlspecialchars($user['name']); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <!-- <label for="access_type">Access Type:</label> -->
                                            <select name="access_type" id="access_type" class="form-control">
                                                <option value="" class="text-mute">Access Type: </option>
                                                <option value="public">Public</option>
                                                <option value="private">Private (for specific user)</option>
                                                <option value="team">Team-based (specific department)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
    
                                <div class="form-group mt-4">
                                    <textarea name="content" id="summernote" class="form-control" rows="10" placeholder="Your message or content here"></textarea>
                                </div>
    
                                
                            </div>

                            <div class="card-footer text-end">
                                
                            </div>
                        </div>
                    </div>
                    <!-- /.Start col -->
                    <!-- Last Col 2 -->
                    <div class="col-lg-3">
                        <!-- Folders -->
                        <div class="row mb-3">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <label for="folder_id">Select Folder</label>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <select name="folder_id" id="folder_id" class="form-control select2">
                                                <option value="">-- None --</option>
                                                <?php
                                                $folders = mysqli_query($conn, "SELECT * FROM folders WHERE company_id = {$_SESSION['company_id']} AND created_by = {$_SESSION['user_id']} ORDER BY name ASC");
                                                while ($folder = mysqli_fetch_assoc($folders)): ?>
                                                    <option value="<?php echo $folder['id']; ?>"
                                                    <?php if (isset($memo['folder_id']) && $memo['folder_id'] == $folder['id']) echo 'selected'; ?>>
                                                        <?php echo htmlspecialchars($folder['name']); ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tags -->
                        <div class="row mb-3">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <label for="folder_id">Tags (comma-seperated)</label>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <input type="text" name="tags" class="form-control" placeholder="e.g., urgent, hr, announcement">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Attachments -->
                        <div class="row mb-3">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <label for="folder_id">Attachment <small>Select File:</small></label>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label><small>(pdf, jpg, jpeg, png, gif, doc, docx)</small></label>
                                            <input type="file" name="file" class="form-control" placeholder="pdf, jpg, jpeg, png, gif, doc, docx">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- / Last Col 2 -->
                    
                </div>
                <!-- /.row (main row) -->
            </form>
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