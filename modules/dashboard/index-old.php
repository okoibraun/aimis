<?php
require_once '../../config/db.php';
require_once '../../config/config.php';
require_once '../../functions/auth_functions.php';
require_once '../../functions/company_functions.php';
require_once '../../functions/user_functions.php';
require_once '../../functions/log_functions.php';
require_once '../../functions/subscription_functions.php';
require_once '../../templates/header.php';
require_once '../../templates/navbar.php';
require_once '../../templates/sidebar.php';

// ensure_logged_in();

// Get user info
$user = get_current_user_data();
$company_id = $user['company_id'];
$is_superadmin = $user['is_superadmin'] ?? false;

// Count data
$total_companies = $is_superadmin ? count_all_companies() : 1;
$total_users = $is_superadmin ? count_all_users() : count_users_by_company($company_id);
$total_logs = count_logs_by_user($user['id']);
$subscription_status = get_company_subscription_status($company_id);
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Dashboard <small>Welcome, <?= htmlspecialchars($user['name']) ?></small></h1>
  </section>

  <section class="content">
    <div class="row">
      <!-- Companies -->
      <?php if ($is_superadmin || has_permission($user['id'], 'view_companies')): ?>
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
          <div class="inner">
            <h3><?= $total_companies ?></h3>
            <p><?= $is_superadmin ? 'Total Companies' : 'Your Company' ?></p>
          </div>
          <div class="icon"><i class="fa fa-building"></i></div>
          <a href="../company/list.php" class="small-box-footer">Manage <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <?php endif; ?>

      <!-- Users -->
      <?php if ($is_superadmin || has_permission($user['id'], 'view_users')): ?>
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
          <div class="inner">
            <h3><?= $total_users ?></h3>
            <p>Users</p>
          </div>
          <div class="icon"><i class="fa fa-users"></i></div>
          <a href="../user/list.php" class="small-box-footer">Manage <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <?php endif; ?>

      <!-- Logs -->
      <?php if ($is_superadmin || has_permission($user['id'], 'view_logs')): ?>
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
          <div class="inner">
            <h3><?= $total_logs ?></h3>
            <p>Recent Logs</p>
          </div>
          <div class="icon"><i class="fa fa-list-alt"></i></div>
          <a href="../logs/activity.php" class="small-box-footer">View <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <?php endif; ?>

      <!-- Subscriptions -->
      <?php if ($is_superadmin || has_permission($user['id'], 'view_subscriptions')): ?>
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
          <div class="inner">
            <h3><?= htmlspecialchars($subscription_status) ?></h3>
            <p>Subscription</p>
          </div>
          <div class="icon"><i class="fa fa-credit-card"></i></div>
          <a href="../subscriptions/billing.php" class="small-box-footer">Manage <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </section>
</div>

<?php require_once '../../templates/footer.php'; ?>
