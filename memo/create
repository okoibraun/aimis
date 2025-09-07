<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../config/db.php');
include("../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Check User Permissions
$page = "add" ?? "create";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

if (isset($_POST['submit'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $access_type = $_POST['access_type'];
    $created_by = $_SESSION['user_id']; // Replace with actual logged-in user id
    $status = $_POST['status'];
    $folder_id = !empty($_POST['folder_id']) ? intval($_POST['folder_id']) : "NULL";
    $recipient = isset($_POST['target_user_id']) ? intval($_POST['target_user_id']) : 0;
    $c0mpany_id = $_SESSION['company_id']; // Assuming company_id is stored in session
    $department = $_SESSION['user_department']; // Assuming user department is stored in session


    $sql = "INSERT INTO memos (sender, recipient, company_id, department, title, content, access_type, folder_id, created_by, created_at, status, target_user_id) 
            VALUES ('$created_by', '$recipient', '$company_id', '$department', '$title', '$content', '$access_type', '$folder_id', '$created_by', NOW(), '$status', '$recipient')";
    

    if (mysqli_query($conn, $sql)) {
        // Optionally, you can handle tags here if needed
        $tags = mysqli_real_escape_string($conn, $_POST['tags']);
        $tag_list = array_map('trim', explode(',', $tags));
        $memo_id = mysqli_insert_id($conn);

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
        
        // Optional: Audit Log
        include_once('../includes/audit_log.php');
        log_audit($conn, $_SESSION['user_id'], 'Create Memo', "Created memo ID $memo_id");

        // Log reading (audit trail)
        $user_id = $_SESSION['user_id']; // Replace with session user id
        $conn->query("INSERT INTO memo_reads (memo_id, user_id, read_at) VALUES ($memo_id, $user_id, NOW())");

        //Notify users about the new memo creation
        // include_once('../includes/notify.php');

        // Notify all users except the uploader
        // $users = mysqli_query($conn, "SELECT id FROM users WHERE id != ".$_SESSION['user_id']);
        // while ($u = mysqli_fetch_assoc($users)) {
        //     notify_user($conn, $u['id'], "New memo uploaded: " . $memo_title);
        // }

        header('Location: index.php?msg=MemoCreated');
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
    <title>AIMIS | Accounts</title>
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
                            <form method="POST" action="">
                                <div class="card-header">
                                    <h3 class="card-title">Create New Memo</h3>
                                    <div class="card-tools">
                                        <a href="index.php" class="btn btn-secondary btn-sm">X</a>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Title:</label>
                                        <input type="text" name="title" class="form-control" required>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Recipient:</label>
                                                <select name="target_user_id" id="" class="form-control" required>
                                                    <option value="">-- Select User --</option>
                                                    <?php
                                                    $users = mysqli_query($conn, "SELECT id, name FROM users WHERE company_id = '{$_SESSION['company_id']}' AND status = 'active' ORDER BY name ASC");
                                                    while ($user = mysqli_fetch_assoc($users)): ?>
                                                        <option value="<?php echo $user['id']; ?>">
                                                            <?php echo htmlspecialchars($user['name']); ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="access_type">Access Type:</label>
                                                <select name="access_type" id="access_type" class="form-control">
                                                    <option value="public">Public</option>
                                                    <option value="private">Private (for specific user)</option>
                                                    <option value="team">Team-based (specific department)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
        
                                    <div class="form-group">
                                        <label>Content:</label>
                                        <textarea name="content" id="summernote" class="form-control" rows="10"></textarea>
                                    </div>
        
                                    <!-- <div class="form-group">
                                        <label>Access Type:</label>
                                        <select name="access_type" class="form-control">
                                            <option value="public">Public</option>
                                            <option value="private">Private</option>
                                            <option value="team">Team-based</option>
                                        </select>
                                    </div> -->
        
        
                                    <!-- <div class="form-group">
                                        <label>Status:</label>
                                        <select name="status" class="form-control">
                                            <option value="draft">Draft</option>
                                            <option value="published">Published</option>
                                            <option value="archived">Archived</option>
                                        </select>
                                    </div> -->
        
                                    <div class="form-group">
                                        <label>Tags (comma-separated):</label>
                                        <input type="text" name="tags" class="form-control" placeholder="e.g., urgent, hr, announcement">
                                    </div>
        
                                    <div class="form-group">
                                        <label for="folder_id">Select Folder</label>
                                        <select name="folder_id" id="folder_id" class="form-control">
                                            <option value="">-- None --</option>
                                            <?php
                                            $folders = mysqli_query($conn, "SELECT * FROM folders ORDER BY name ASC");
                                            while ($folder = mysqli_fetch_assoc($folders)): ?>
                                                <option value="<?php echo $folder['id']; ?>"
                                                <?php if (isset($memo['folder_id']) && $memo['folder_id'] == $folder['id']) echo 'selected'; ?>>
                                                    <?php echo htmlspecialchars($folder['name']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="card-footer text-end">
                                    <a href="index.php" class="btn btn-danger">Cancel</a>
                                    <button type="submit" name="submit" class="btn btn-success">Save Memo</button>
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