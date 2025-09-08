<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$company_id = $_SESSION['company_id'];
$empid = isset($_GET['emp_id']) ?? 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $eid = $_POST['employee_id'];
    $type = $_POST['contract_type'];
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $terms = $_POST['terms'];

    $stmt = $conn->prepare("INSERT INTO contracts (company_id, employee_id, contract_type, start_date, end_date, terms) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissss", $company_id, $eid, $type, $start, $end, $terms);

    if ($stmt->execute()) {
        $contract_id = $conn->insert_id;
        $uploadDir = "../../uploads/contracts/";

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($_FILES['contract_documents']['name'] as $key => $name) {
            $tmpName = $_FILES['contract_documents']['tmp_name'][$key];
            $error = $_FILES['contract_documents']['error'][$key];
            $type = $_FILES['contract_documents']['type'][$key];
            $file_link = "{$name}_contract_file_" . time();
            $file_name = $name;


            if ($error === UPLOAD_ERR_OK) {
                $targetFile = $uploadDir . basename($file_link);

                $insert_stmt = $conn->prepare("INSERT INTO contract_files (company_id, contract_id, file_name, file_link, file_type) VALUES (?, ?, ?, ?, ?)");
                $insert_stmt->bind_param("iisss", $company_id, $contract_id, $file_name, $file_link, $type);
                $insert_stmt->execute();

                if (move_uploaded_file($tmpName, $targetFile)) {
                    $_SESSION['message'] = "Uploaded: $file_name<br>";
                } else {
                    $_SESSION['error'] = "Failed to upload: $file_name<br>";
                }
            } else {
                $_SESSION['error'] = "Error uploading: $file_name (Error code: $error)<br>";
            }
        }
        header("Location: ../employees/view_employee.php?id=$eid&msg=contract_saved");
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
    }
}

?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Payroll - Manage Employee Contracts</title>
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
                    <div class="content-header mt-3 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Add New Contracts</h3>
                                <div class="card-tools">
                                    <a href="view_contracts.php" class="btn btn-secondary">Back to Contracts</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Contract Details</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label>Select Employee</label>
                                                <select name="employee_id" class="select2 form-control" required>
                                                    <option value="">-- Select --</option>
                                                    <?php
                                                        $res = $conn->query("SELECT id, first_name, last_name FROM employees WHERE company_id = $company_id");
                                                        while ($row = $res->fetch_assoc()):
                                                    ?>
                                                        <option value="<?= $row['id'] ?>" <?= ($empid == $row['id']) ? 'selected' : ''; ?>><?= $row['first_name'] . ' ' . $row['last_name'] ?></option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
            
                                            <div class="form-group mb-3">
                                                <label>Contract Type</label>
                                                <select name="contract_type" class="form-control" required>
                                                    <option value="">-- Choose --</option>
                                                    <option value="full-time">Full-time</option>
                                                    <option value="part-time">Part-time</option>
                                                    <option value="contract">Contract</option>
                                                    <option value="intern">Intern</option>
                                                </select>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Start Date</label>
                                                        <input type="date" name="start_date" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>End Date</label>
                                                        <input type="date" name="end_date" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
            
                                            <div class="form-group mb-3">
                                                <label>Contract Terms</label>
                                                <textarea name="terms" class="form-control" rows="4" placeholder="Enter contract terms..."></textarea>
                                            </div>
            
                                            <button type="submit" class="btn btn-primary float-end">Save Contract</button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                Upload Contract Documents/Files <small>Terms, agreement, etc.</small> (optional)
                                            </h3>
                                            <div class="card-tools">
            
                                            </div>
                                        </div>
                                        <div class="card-header">
                                            <div class="form-group">
                                                <label for="contract_documents">Select files to upload (.pdf, .doc, .docx, .jpg, .png, .jpeg)</label>
                                                <input type="file" name="contract_documents[]" multiple id="" class="form-control" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
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

<?php include '../../includes/footer.php'; ?>