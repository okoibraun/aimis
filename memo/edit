<?php
session_start();
// Include database connection and header
include('../config/db.php');
include("../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

// Check User Permissions
$page = "edit";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$memo_id = intval($_GET['id']);

// Fetch memo details
$query = "SELECT * FROM memos WHERE id = $memo_id LIMIT 1";
$result = mysqli_query($conn, $query);
$memo = mysqli_fetch_assoc($result);

if (!$memo) {
    echo "Memo not found.";
    exit();
}

if (isset($_POST['submit'])) {
    //Save current memo version to history table
    // Save current version before updating
    $current = mysqli_fetch_assoc(mysqli_query($conn, "SELECT title, content FROM memos WHERE id = $memo_id"));

    mysqli_query($conn, "
        INSERT INTO memo_versions (memo_id, title, content, edited_by) 
        VALUES ($memo_id, '".mysqli_real_escape_string($conn, $current['title'])."', '".mysqli_real_escape_string($conn, $current['content'])."', ".$_SESSION['user_id'].")
    ");

    //Proceed with update
    // Update memo details
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $access_type = $_POST['access_type'];
    $status = $_POST['status'];
    $updated_by = 1; // Replace with session user id
    // Update tags
    $tags = mysqli_real_escape_string($conn, $_POST['tags']);
    $tag_list = array_map('trim', explode(',', $tags));
    //Folders
    $folder_id = !empty($_POST['folder_id']) ? intval($_POST['folder_id']) : "NULL";
    

    $sql = "UPDATE memos 
            SET title='$title', content='$content', access_type='$access_type', folder_id='$folder_id', status='$status', updated_by='$updated_by', updated_at=NOW()
            WHERE id=$memo_id";

    if (mysqli_query($conn, $sql)) {

        // Update tags
        // Delete old tags
        mysqli_query($conn, "DELETE FROM memo_tags WHERE memo_id = $memo_id");

        foreach ($tag_list as $tag_name) {
            if (!empty($tag_name)) {
                $tag_check = mysqli_query($conn, "SELECT id FROM tags WHERE name = '$tag_name'");
                if (mysqli_num_rows($tag_check) > 0) {
                    $tag = mysqli_fetch_assoc($tag_check);
                    $tag_id = $tag['id'];
                } else {
                    mysqli_query($conn, "INSERT INTO tags (name) VALUES ('$tag_name')");
                    $tag_id = mysqli_insert_id($conn);
                }
                mysqli_query($conn, "INSERT INTO memo_tags (memo_id, tag_id) VALUES ('$memo_id', '$tag_id')");
            }
        }

        // Optional: Audit Log
        include_once('../includes/audit_log.php');
        log_audit($conn, $_SESSION['user_id'], "Update Memo", "Updated memo ID $memo_id");
        // Redirect to index page with success message
        header('Location: ./?msg=MemoUpdated');
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }

}


// Fetch existing tags
$tag_query = "SELECT t.name FROM tags t INNER JOIN memo_tags mt ON t.id = mt.tag_id WHERE mt.memo_id = $memo_id";
$tag_result = mysqli_query($conn, $tag_query);
$tag_names = [];
while ($tag = mysqli_fetch_assoc($tag_result)) {
    $tag_names[] = $tag['name'];
}
$tags_str = implode(', ', $tag_names);

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
                        <h2>Edit Memo</h2>
                        <form method="POST" action="">
                            <div class="form-group">
                                <label>Title:</label>
                                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($memo['title']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Content:</label>
                                <textarea name="content" id="summernote" class="form-control" rows="10"><?php echo htmlspecialchars($memo['content']); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Access Type:</label>
                                <select name="access_type" class="form-control">
                                    <option value="public" <?php if($memo['access_type']=='public') echo 'selected'; ?>>Public</option>
                                    <option value="private" <?php if($memo['access_type']=='private') echo 'selected'; ?>>Private</option>
                                    <option value="team" <?php if($memo['access_type']=='team') echo 'selected'; ?>>Team-based</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Status:</label>
                                <select name="status" class="form-control">
                                    <option value="draft" <?php if($memo['status']=='draft') echo 'selected'; ?>>Draft</option>
                                    <option value="published" <?php if($memo['status']=='published') echo 'selected'; ?>>Published</option>
                                    <option value="archived" <?php if($memo['status']=='archived') echo 'selected'; ?>>Archived</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Tags (comma-separated):</label>
                                <input type="text" name="tags" class="form-control" value="<?php echo htmlspecialchars($tags_str); ?>">
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

                            <button type="submit" name="submit" class="btn btn-success">Update Memo</button>
                            <a href="index.php" class="btn btn-danger">Cancel</a>
                        </form>
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