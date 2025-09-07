<?php
require_once '../../includes/helpers.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../login.php');
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) exit('Invoice ID is required');

$invoice = $db->query("SELECT * FROM sales_invoices WHERE id = ?", [$id])->fetch();
if (!$invoice) exit("Invoice not found.");

$customers = $db->query("SELECT id, company_name FROM sales_customers");
$orders = $db->query("SELECT id, order_number FROM sales_orders");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoice_number = $_POST['invoice_number'];
    $invoice_date = $_POST['invoice_date'];
    $due_date = $_POST['due_date'];
    $customer_id = $_POST['customer_id'];
    $order_id = $_POST['order_id'] ?: null;
    $total_amount = $_POST['total_amount'];
    $notes = $_POST['notes'];
    $status = $_POST['status'];

    // $db->update('sales_invoices', [
    //     'invoice_number' => $invoice_number,
    //     'invoice_date' => $invoice_date,
    //     'due_date' => $due_date,
    //     'customer_id' => $customer_id,
    //     'order_id' => $order_id,
    //     'total_amount' => $total_amount,
    //     'notes' => $notes,
    //     'status' => $status
    // ], ['id' => $id]);

    $stmt = $db->prepare("UPDATE sales_invoices SET invoice_number = ?, invoice_date = ?, due_date = ?, customer_id = ?, order_id = ?, total_amount = ?, notes = ?, status = ? WHERE id = ?");
    $stmt->execute([
        $invoice_number,
        $invoice_date,
        $due_date,
        $customer_id,
        $order_id,
        $total_amount,
        $notes,
        $status,
        $id
    ]);
    if ($stmt->errorCode() !== '00000') {
        exit("Error updating invoice: " . implode(", ", $stmt->errorInfo()));
    }

    $invoice_id = $new_invoice_id_or_existing;

    if (!empty($_POST['doc_ids'])) {
        // Clear previous links
        $conn->prepare("DELETE FROM document_links WHERE module = 'sales_invoice' AND module_ref_id = ?")
            ->execute([$invoice_id]);

        // Insert new links
        foreach ($_POST['doc_ids'] as $doc_id) {
            $conn->prepare("INSERT INTO document_links (document_id, module, module_ref_id, linked_by) VALUES (?, 'sales_invoice', ?, ?)")
                ->execute([$doc_id, $invoice_id, $_SESSION['user_id']]);
        }
    }

    // Log the update
    require_once '../../../../functions/log_functions.php';
    log_activity($_SESSION['user_id'], $_SESSION['company_id'], 'update_invoice', "Updated invoice #$id");
    $_SESSION['success'] = "Invoice updated successfully.";
    // Redirect to invoices list

    redirect('invoices.php');
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Sales - Invoices</title>
    <?php include_once("../../../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">
          

            <div class="content-wrapper">
                <section class="content-header">
                    <h1>Create Invoice</h1>
                </section>

                <section class="content">
                    <form method="post" class="box box-primary">
                        <div class="box-body">

                            <div class="form-group">
                                <label for="invoice_number">Invoice Number</label>
                                <input type="text" name="invoice_number" value="<?= htmlspecialchars($invoice['invoice_number'] ?? '') ?>" class="form-control" required>
                            </div>

                            <div class="form-group">
                            <label for="invoice_date">Invoice Date</label>
                            <input type="date" name="invoice_date" id="invoice_date" value="<?= $invoice['invoice_date'] ?? '' ?>" class="form-control" required>
                            </div>

                            <div class="form-group">
                            <label for="due_date">Due Date</label>
                            <input type="date" name="due_date" id="due_date" value="<?= $invoice['due_date'] ?? '' ?>" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="customer_id">Customer</label>
                                <select name="customer_id" class="form-control" required>
                                    <option value="">-- Select Customer --</option>
                                    <?php foreach ($customers as $cust): ?>
                                        <option value="<?= $cust['id'] ?>" <?= isset($invoice['customer_id']) && $invoice['customer_id'] == $cust['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cust['company_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                            <label for="order_id">Linked Order (Optional)</label>
                            <select name="order_id" id="order_id" class="form-control">
                                <option value="">-- Select Order --</option>
                                <?php foreach ($orders as $ord): ?>
                                <option value="<?= $ord['id'] ?>"><?= htmlspecialchars($ord['order_number']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            </div>

                            <div class="form-group">
                                <label for="total_amount">Total Amount</label>
                                <input type="number" step="0.01" name="total_amount" id="total_amount" value="<?= $invoice['total_amount'] ?? '' ?>" class="form-control" required>
                            </div>

                            <?php if (isset($invoice)): ?>
                            <div class="form-group">
                                <label for="status">Invoice Status</label>
                                <select name="status" class="form-control">
                                <?php foreach (['unpaid', 'partial', 'paid', 'overdue'] as $status): ?>
                                    <option value="<?= $status ?>" <?= $invoice['status'] === $status ? 'selected' : '' ?>>
                                    <?= ucfirst($status) ?>
                                    </option>
                                <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea name="notes" id="notes" class="form-control" rows="4"><?= $invoice['notes'] ?? '' ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Attach Documents</label>
                                <select name="doc_ids[]" class="form-control" multiple>
                                    <?php
                                    $docs = $conn->query("SELECT id, title FROM documents ORDER BY created_at DESC");
                                    foreach ($docs as $d) {
                                        echo "<option value='{$d['id']}'>" . htmlspecialchars($d['title']) . "</option>";
                                    }
                                    ?>
                                </select>
                                <small>Use Ctrl or Shift to select multiple documents</small>
                            </div>

                        </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Save Invoice</button>
                            <a href="invoices.php" class="btn btn-default">Cancel</a>
                        </div>
                    </form>
                </section>
            </div>
          

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
