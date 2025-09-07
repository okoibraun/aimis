<?php
require_once '../../config/db.php';
require_once '../../functions/helpers.php';
require_once '../../functions/company_functions.php';
require_once '../../functions/auth_functions.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'superadmin'])) {
    redirect('../../login.php');
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_name = sanitize_input($_POST['group_name']);
    $company_id = $_POST['company_id'] ?? [];

    if (empty($group_name)) {
        $errors[] = "Group name is required.";
    } elseif (empty($company_id)) {
        $errors[] = "Select at least one company for the group.";
    } else {
        // if (create_company_group($group_name, $company_id)) {
        if(mysqli_query($conn, "INSERT INTO company_groups (company_id, name) VALUES ('$company_id', '$group_name')")) {
            $success = true;
        } else {
            $errors[] = "Failed to create group.";
        }
    }
}

// Show all companies for selection (limited for admins)
$all_companies = ($_SESSION['role'] === 'superadmin') 
    ? get_all_companies() 
    : get_companies_by_group($_SESSION['company_id']);

$groups = get_all_company_groups();
// $groups = mysqli_query($conn, "SELECT * FROM company_groups ORDER BY created_at DESC");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Company Groups</title>
    <?php include_once("../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">

            <div class="content-wrapper">
              <section class="content-header">
                <div class="container-fluid">
                  <h1>Company Groups</h1>
                </div>
              </section>

              <section class="content">
                <div class="container-fluid">

                  <?php if ($success): ?>
                    <div class="alert alert-success">Group created successfully.</div>
                  <?php endif; ?>

                  <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger"><?= implode('<br>', $errors); ?></div>
                  <?php endif; ?>

                  <form method="post">
                    <div class="form-group">
                      <label for="group_name">Group Name</label>
                      <input type="text" name="group_name" id="group_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                      <label>Assign Companies to Group</label>
                      <select name="company_id" class="form-control" required>
                        <?php foreach ($all_companies as $c): ?>
                          <option value="<?= $c['id']; ?>"><?= htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Create Group</button>
                  </form>

                  <hr>

                  <h3>Existing Groups</h3>
                  <ul class="list-group">
                    <?php foreach ($groups as $g) { ?>
                      <li class="list-group-item mb-2">
                        <strong><?= $g['name']; ?></strong><br>
                        Companies: <?= $g['company_names']; ?>
                      </li>
                    <?php } ?>
                  </ul>

                </div>
              </section>
            </div>

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
