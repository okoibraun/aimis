<?php
session_start();
include '../../config/db.php';
//include '../../includes/auth_employee.php';

$emp_id = $_SESSION['employee_id'] ?? $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $bank_name = $_POST['bank_name'];
    $account_number = $_POST['account_number'];
    $account_name = $_POST['account_name'];

    $stmt = $conn->prepare("UPDATE employees SET phone=?, address=?, bank_name=?, account_number=?, account_name=? WHERE id=?");
    $stmt->bind_param("sssssi", $phone, $address, $bank_name, $account_number, $account_name, $emp_id);
    $stmt->execute();
    $msg = "Profile updated.";
}

$emp = $conn->query("SELECT * FROM employees WHERE id = $emp_id")->fetch_assoc();
?>

<?php include '../../includes/header.php'; ?>
<div class="container mt-4">
    <h4>Update Your Profile</h4>
    <?php if (isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>
    <form method="post">
        <input name="phone" value="<?= $emp['phone'] ?>" class="form-control mb-2" placeholder="Phone">
        <input name="address" value="<?= $emp['address'] ?>" class="form-control mb-2" placeholder="Address">
        <input name="bank_name" value="<?= $emp['bank_name'] ?>" class="form-control mb-2" placeholder="Bank Name">
        <input name="account_number" value="<?= $emp['account_number'] ?>" class="form-control mb-2" placeholder="Account Number">
        <input name="account_name" value="<?= $emp['account_name'] ?>" class="form-control mb-2" placeholder="Account Name">
        <button class="btn btn-primary">Update</button>
    </form>
</div>
<?php include '../../includes/footer.php'; ?>
