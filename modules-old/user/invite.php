<?php
require_once '../../config/db.php';
require_once '../../functions/helpers.php';
require_once '../../functions/auth_functions.php';
require_once '../../functions/user_functions.php';
require_once '../../functions/invitation_functions.php';
require_once '../../functions/role_functions.php';
require_once("../../functions/company_functions.php");

if (!isset($_SESSION['user_id'])) {
    redirect('../../login.php');
}

if (!in_array($_SESSION['role'], $roles)) {
    die('Unauthorized access.');
}

$errors = [];
$success = false;
$available_roles = get_available_roles_for_user($_SESSION);

$company_id = ($_SESSION['role'] === 'system') ? ($_POST['company_id'] ?? null) : $_SESSION['company_id'];

$companies = ($_SESSION['role'] === 'system') ? get_all_companies() : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $role = sanitize_input($_POST['role']);

    if (!$email || !$role || !$company_id) {
        $errors[] = "All fields are required.";
    }

    if (!user_can_manage_company($_SESSION, get_company_by_id($company_id))) {
        $errors[] = "You are not authorized to invite users to this company.";
    }

    if (empty($errors)) {
        $token = bin2hex(random_bytes(32));
        if (create_invitation($email, $role, $company_id, $token)) {
            send_invitation_email($email, $token);
            $success = true;
        } else {
            $errors[] = "Failed to send invitation. Email may already be in use or pending.";
        }
    }
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Users</title>
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
                  <h1>Invite User</h1>
                </div>
              </section>
              <hr class="mb-5">

              <section class="content">

                <?php if (!empty($errors)): ?>
                  <div class="alert alert-danger">
                    <ul>
                      <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error); ?></li>
                      <?php endforeach; ?>
                    </ul>
                  </div>
                <?php endif; ?>

                <?php if ($success): ?>
                  <div class="alert alert-success">Invitation sent successfully.</div>
                <?php endif; $success = false; ?>

                <?php if (!empty($errors)): ?>
                  <div class="alert alert-danger"><?= implode('<br>', $errors); ?></div>
                <?php endif; ?>
                <?php $errors = []; ?>

                <div class="container-fluid">
                  <form method="post">
                    <div class="card">
                      <div class="card-header">Invite New User</div>
                      <div class="card-body">
                        <div class="row">
                          <div class="col-3">
                            <div class="form-group">
                              <label for="email">Invitee Email</label>
                              <input type="email" name="email" class="form-control" required>
                            </div>
                          </div>
                          <div class="col-3">
                            <?php if ($_SESSION['role'] === 'system') { ?>
                            <div class="form-group">
                              <label for="company_id">Assign to Company</label>
                              <select name="company_id" class="form-control select2" required>
                                <option value="">Select Company</option>
                                <?php foreach ($companies as $c): ?>
                                  <option value="<?= $c['id']; ?>"><?= htmlspecialchars($c['name']); ?></option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                            <?php } else { ?>
                            <input type="hidden" name="company_id" value="<?= $_SESSION['company_id']; ?>">
                            <?php } ?>
                          </div>
                          <div class="col-3">
                            <div class="form-group">
                              <label for="role">Role</label>
                              <select name="role" class="form-control select2" required>
                                <?php foreach ($available_roles as $r): ?>
                                  <option value="<?= $r; ?>"><?= ucfirst($r); ?></option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                          </div>
                          <div class="col-3">
                            <div class="form-group">
                              <br>
                              <button type="submit" class="btn btn-primary">Send Invitation</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </form>

                  <div class="row mt-5">
                    <!-- <h3 class="mb4">
                      Existing invitations
                    </h3> -->
                    <div class="col-12">
                      <div class="card">
                        <div class="card-header">
                          <h4 class="card-title">Invitations List</h4>
                          <div class="card-tools">
                            <a href="list.php" class="btn btn-sm btn-secondary">Refresh</a>
                          </div>
                        </div>
                        <div class="card-body">
                          <table id="zero-config" class="table table-bordered">
                            <thead>
                              <tr>
                                <th>Email</th>
                                <th>Assigned to Company</th>
                                <th>Role</th>
                                <th>Used</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $invites = mysqli_query($conn, "SELECT * FROM invitations"); foreach($invites as $invite) { ?>
                              <tr>
                                <td><?= $invite['email']; ?></td>
                                <td><?= ($company = $conn->query("SELECT name FROM companies WHERE id={$invite['company_id']}")->fetch_assoc()) ? $company['name'] : "none"; ?></td>
                                <td><?= $invite['role']; ?></td>
                                <td><?= ($invite['is_used'] == 1) ? "Used" : "Not Used"; ?></td>
                              </tr>
                              <?php } ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
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
