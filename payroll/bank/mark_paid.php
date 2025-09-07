<?php
include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $month = $_POST['month'];

    $stmt = $conn->prepare("UPDATE payroll SET paid_status = 1 WHERE month = ?");
    $stmt->bind_param("s", $month);
    $stmt->execute();

    echo "Salaries marked as paid for $month.";
}
?>

<form method="post" class="p-4">
    <label>Month:</label>
    <input type="month" name="month" required>
    <button class="btn btn-success">Mark as Paid</button>
</form>
