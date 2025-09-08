<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';
include("../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
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

$id = (int) ($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM crm_tags WHERE id = ? AND company_id = ?");
$stmt->bind_param('ii', $id, $_SESSION['company_id']);
$stmt->execute();
$result = $stmt->get_result();
$tag = $result->fetch_assoc();

if (!$tag) {
    die('Tag not found.');
}

$page_title = "Edit Tag";
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | CRM - Tags</title>
    <?php include_once("../../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">
          

          <!-- Main Content -->
          <div class="content-wrapper">
            <section class="content-header">
                <h1>Edit Tag <small><?= ucfirst($tag['module']) ?></small></h1>
            </section>

            <section class="content">
                <div class="box box-primary">
                <form action="update.php" method="POST">
                    <input type="hidden" name="id" value="<?= $tag['id'] ?>">

                    <div class="box-body">
                    <div class="form-group">
                        <label>Module</label>
                        <input type="text" class="form-control" value="<?= ucfirst($tag['module']) ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label for="name">Tag Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($tag['name']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="color">Color (hex)</label>
                        <input type="color" name="color" class="form-control" value="<?= htmlspecialchars($tag['color']) ?>">
                    </div>
                    </div>

                    <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="index.php?module=<?= $tag['module'] ?>" class="btn btn-default">Cancel</a>
                    </div>
                </form>
                </div>
            </section>
            </div>
          

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
