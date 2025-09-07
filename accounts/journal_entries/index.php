<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');
include("../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Check User Permissions
$page = "list";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
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
                    <h1>Journal Entries</h1>
                </section>
                <section class="content">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Entries</h3>
                            <div class="card-tools">
                                <a href="add" class="btn btn-primary">Add New Entry</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered DataTable">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Total Debit</th>
                                        <th>Total Credit</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // $query = "";
                                    if(in_array($_SESSION['user_role'], super_roles())) {
                                        $query = "SELECT je.*, 
                                                (SELECT SUM(debit) FROM journal_lines WHERE journal_entry_id = je.id) AS total_debit,
                                                (SELECT SUM(credit) FROM journal_lines WHERE journal_entry_id = je.id) AS total_credit
                                                FROM journal_entries je WHERE je.company_id = $company_id ORDER BY entry_date DESC";
                                    } else if(in_array($_SESSION['user_role'], system_users())) {
                                        $query = "SELECT je.*, 
                                                (SELECT SUM(debit) FROM journal_lines WHERE journal_entry_id = je.id) AS total_debit,
                                                (SELECT SUM(credit) FROM journal_lines WHERE journal_entry_id = je.id) AS total_credit
                                                FROM journal_entries je ORDER BY entry_date DESC";
                                    } else {
                                        $query = "SELECT je.*, 
                                                (SELECT SUM(debit) FROM journal_lines WHERE journal_entry_id = je.id) AS total_debit,
                                                (SELECT SUM(credit) FROM journal_lines WHERE journal_entry_id = je.id) AS total_credit
                                                FROM journal_entries je WHERE je.company_id = $company_id AND je.user_id = $user_id OR je.employee_id = $employee_id ORDER BY entry_date DESC";
                                    }
                                    $result = mysqli_query($conn, $query);
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>
                                                <td>{$row['entry_date']}</td>
                                                <td>{$row['description']}</td>
                                                <td>{$row['total_debit']}</td>
                                                <td>{$row['total_credit']}</td>
                                                <td>
                                                <a href='edit?id={$row['id']}' class='btn btn-sm btn-warning'>Edit</a>
                                                <a href='delete?id={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Delete this entry?\")'>Delete</a>
                                                </td>
                                            </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
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