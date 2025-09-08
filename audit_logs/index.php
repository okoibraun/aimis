<?php
session_start();
include('../config/db.php');

// Admin only
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$logs = mysqli_query($conn, "
    SELECT audit_logs.*, users.name AS user_name 
    FROM audit_logs 
    LEFT JOIN users ON audit_logs.user_id = users.id
    ORDER BY audit_logs.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Audit Logs - Memo System</title>
    <link rel="stylesheet" href="../assets/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2>Audit Logs</h2>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>User</th>
                <th>Action</th>
                <th>Description</th>
                <th>Date/Time</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($logs)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                <td><?php echo htmlspecialchars($row['action']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</div>

</body>
</html>