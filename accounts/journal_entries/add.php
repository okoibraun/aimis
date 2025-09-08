<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');
include("../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

// Check User Permissions
$page = "add";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $date = $_POST['entry_date'];
  $desc = $_POST['description'];

  // Insert journal entry
  $stmt = $conn->prepare("INSERT INTO journal_entries (company_id, user_id, employee_id, entry_date, description) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("iiiss", $company_id, $user_id, $employee_id, $date, $desc);
  $stmt->execute();
  $entry_id = $stmt->insert_id;

  // Insert journal lines
  foreach ($_POST['account_id'] as $index => $acc_id) {
      $debit = $_POST['debit'][$index] ?: 0;
      $credit = $_POST['credit'][$index] ?: 0;

      $stmt = $conn->prepare("INSERT INTO journal_lines (journal_entry_id, account_id, debit, credit) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("iidd", $entry_id, $acc_id, $debit, $credit);
      $stmt->execute();
  }

  header("Location: ./");
  exit();
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Accounts - List Journal Entries</title>
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
                <section class="content-header mt-3 mb-3">
                  <h1>Add Journal Entry</h1>
                </section>

                <section class="content">
                  <form method="POST" action="" class="row">
                    <div class="col-5">
                      <div class="card">
                        <div class="card-header">
                          <h3 class="card-title">Entry Details</h3>
                        </div>
                        <div class="card-body">
                          <div class="form-group">
                            <label>Entry Date</label>
                            <input type="date" name="entry_date" class="form-control" required>
                          </div>
                          <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="2" required></textarea>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="col">
                      <div class="card">
                        <div class="card-header">
                          <h3 class="card-title">Journal Lines</h3>
                          <div class="card-tools">
                            <button type="button" class="btn btn-success btn-sm" onclick="addLine()">+</button>
                          </div>
                        </div>
                        <div class="card-body">
                          <table class="table table-bordered" id="journal-lines">
                            <thead>
                              <tr>
                                <th>Account</th>
                                <th>Debit</th>
                                <th>Credit</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td>
                                  <select name="account_id[]" class="form-control" required>
                                    <option value="">Select Account</option>
                                    <?php
                                    $res = mysqli_query($conn, "SELECT id, account_name FROM accounts WHERE company_id = $company_id");
                                    while ($row = mysqli_fetch_assoc($res)) {
                                        echo "<option value='{$row['id']}'>{$row['account_name']}</option>";
                                    }
                                    ?>
                                  </select>
                                </td>
                                <td>
                                  <input type="number" step="0.01" name="debit[]" class="form-control">
                                </td>
                                <td>
                                  <input type="number" step="0.01" name="credit[]" class="form-control">
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>

                    <div class="form-group mt-3 mb-3">
                      <button type="submit" class="btn btn-primary">Save Entry</button>
                      <a href="./" class="btn btn-secondary">Cancel</a>
                    </div>
                    

                  </form>
                </section>

                <script>
                function addLine() {
                  const row = document.querySelector('#journal-lines tbody tr');
                  const clone = row.cloneNode(true);
                  clone.querySelectorAll('input').forEach(input => input.value = '');
                  document.querySelector('#journal-lines tbody').appendChild(clone);
                }
                </script>
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