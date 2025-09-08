<?phpsession_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');
include("../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

// Check User Permissions
$page = "edit";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$products_tbl = 'inventory_products' ?? 'sales_products';
$id = $_GET['id'];
$wo = $conn->query("SELECT * FROM production_work_orders WHERE id = $id")->fetch_assoc();
$products = $conn->query("SELECT id, name FROM $products_tbl");
$boms = $conn->query("SELECT id, version FROM production_bom");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Production - Work Orders</title>
    <?php include_once("../../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">

            <div class="content-wrapper">
                <section class="content-header"><h1>Edit Work Order</h1></section>
                <section class="content">
                    <form action="save.php" method="post">
                        <input type="hidden" name="id" value="<?= $wo['id'] ?>">
                        <div class="form-group">
                            <label>Order Code</label>
                            <input type="text" name="order_code" class="form-control" value="<?= $wo['order_code'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Product</label>
                            <select name="product_id" class="form-control" required>
                                <?php while($p = mysqli_fetch_assoc($products)): ?>
                                    <option value="<?= $p['id'] ?>" <?= $p['id'] == $wo['product_id'] ? 'selected' : '' ?>>
                                        <?= $p['name'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>BOM Version</label>
                            <select name="bom_id" class="form-control">
                                <option value="">Select BOM (optional)</option>
                                <?php while($b = mysqli_fetch_assoc($boms)): ?>
                                    <option value="<?= $b['id'] ?>" <?= $b['id'] == $wo['bom_id'] ? 'selected' : '' ?>>
                                        BOM #<?= $b['id'] ?> - <?= $b['version'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Quantity to Produce</label>
                            <input type="number" name="quantity" class="form-control" value="<?= $wo['quantity'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Scheduled Start</label>
                            <input type="datetime-local" name="scheduled_start" class="form-control"
                                value="<?= date('Y-m-d\TH:i', strtotime($wo['scheduled_start'])) ?>">
                        </div>
                        <div class="form-group">
                            <label>Scheduled End</label>
                            <input type="datetime-local" name="scheduled_end" class="form-control"
                                value="<?= date('Y-m-d\TH:i', strtotime($wo['scheduled_end'])) ?>">
                        </div>
                        <button type="submit" name="action" value="update" class="btn btn-success">Update Work Order</button>
                    </form>
                </section>
            </div>

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
.php'; ?>
