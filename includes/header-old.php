<?php
// Fetch latest 5 unread notifications
$notifs = mysqli_query($conn, "
    SELECT * FROM notifications 
    WHERE user_id = ".$_SESSION['user_id']." 
    ORDER BY created_at DESC 
    LIMIT 5
");

$unread_count = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) as total FROM notifications 
    WHERE user_id = ".$_SESSION['user_id']." AND is_read = 0
"))['total'];
?>

<li class="nav-item dropdown">
  <a class="nav-link" data-toggle="dropdown" href="#">
    <i class="far fa-bell"></i>
    <?php if($unread_count > 0): ?>
      <span class="badge badge-danger navbar-badge"><?php echo $unread_count; ?></span>
    <?php endif; ?>
  </a>
  <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
    <span class="dropdown-header"><?php echo $unread_count; ?> Unread Notifications</span>
    <div class="dropdown-divider"></div>
    <?php while($n = mysqli_fetch_assoc($notifs)): ?>
      <a href="#" class="dropdown-item">
        <i class="fas fa-envelope mr-2"></i> <?php echo htmlspecialchars(substr($n['message'],0,30)); ?>...
        <span class="float-right text-muted text-sm"><?php echo date('H:i', strtotime($n['created_at'])); ?></span>
      </a>
      <div class="dropdown-divider"></div>
    <?php endwhile; ?>
    <a href="/notifications/index.php" class="dropdown-item dropdown-footer">See All Notifications</a>
  </div>
</li>