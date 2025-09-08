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

$memo = $conn->query("SELECT * FROM memos WHERE id = $memo_id")->fetch_assoc();

if (isset($_POST['submit'])) {
    $forward_to = mysqli_real_escape_string($conn, $_POST['forward_to']);
    $forward_note = mysqli_real_escape_string($conn, $_POST['forward_note']);
    $is_forwarded = intval('1');
    $status = 'forwarded';

    $sql = "UPDATE memos SET status=?, forward_to=?, forward_note=?, is_forwarded=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sisii', $status, $forward_to, $forward_note, $is_forwarded, $memo_id);

    if ($stmt->execute()) {

        //Notify users about the new memo creation
        include_once('../includes/notify.php');

        // Notify all users except the uploader
        $user = $conn->query("SELECT id FROM users WHERE id = {$forward_to} AND company_id = '{$_SESSION['company_id']}' AND status = 'active'")->fetch_assoc();
        //while ($u = mysqli_fetch_assoc($users)) {
            notify_user($conn, $user['id'], "New Forwarded Memo: " . $memo_title);
        //}

        // Log reading (memo audit trail)
        $conn->query("INSERT INTO memo_reads (company_id, employee_id, memo_id, user_id, forwarded_at) VALUES ($company_id, $employee_id, $memo_id, $user_id, NOW())");

        // Log Activity
        include_once('../functions/log_functions.php');
        log_activity($user_id, $company_id, 'forward_memo', "Forwarded Memo: {$title} to {$recipient}");

        $_SESSION['success'] = "Memo forwarded successfully.";
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
                            <form method="POST">
                                <div class="card-header">
                                    <h3 class="card-title">Forward [ <?= $memo['title'] ?> ]</h3>
                                    <div class="card-tools">
                                        <a href="memo?id=<?= $memo['id'] ?>" class="btn btn-secondary btn-sm">X</a>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="forward_to">Forward to User</label>
                                                <select name="forward_to" class="form-control" required>
                                                    <option value="">-- Select User --</option>
                                                    <?php
                                                    $users = mysqli_query($conn, "SELECT id, name FROM users WHERE company_id = '{$_SESSION['company_id']}' AND status = 'active' ORDER BY name ASC");
                                                    while ($user = mysqli_fetch_assoc($users)): ?>
                                                    <?php if ($user['id'] == $_SESSION['user_id']) continue; // Skip current user ?>
                                                        <option value="<?php echo $user['id']; ?>">
                                                            <?php echo htmlspecialchars($user['name']); ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Date Created:</label>
                                                <input type="text" class="form-control text-mute" value="<?= $memo['created_at'] ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mt-2">
                                        <label for="forward_note">Notes</label>
                                        <textarea name="forward_note" id="summernote" rows="10" class="form-control"></textarea>
                                    </div>
                                </div>

                                <div class="card-footer text-end">
                                    <a href="memo?id=<?= $memo['id'] ?>" class="btn btn-danger">Cancel</a>
                                    <button type="submit" name="submit" class="btn btn-success">Forward</button>
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