<?php
$isEdit = isset($contact);
?>

<form method="POST" action="save.php">
  <?php if ($isEdit): ?>
    <input type="hidden" name="id" value="<?= $contact['id'] ?>">
  <?php endif; ?>

  <div class="form-group">
    <label for="lead_id">Leads</label>
    <select name="lead_id" id="" class="form-control select2">
      <option value="">-- Select Lead --</option>
      <?php $leads = $conn->query("SELECT id, title FROM sales_customers WHERE company_id = $company_id AND customer_type = 'lead'"); foreach($leads as $lead) { ?>
        <option value="<?= $lead['id'] ?>" <?= $isEdit && $contact['lead_id'] == $lead['id'] ? 'selected' : '' ?>><?= $lead['title'] ?></option>
      <?php } ?>
    </select>
  </div>

  <div class="form-group">
    <label>Full Name</label>
    <input type="text" name="full_name" class="form-control" required value="<?= $isEdit ? htmlspecialchars($contact['full_name']) : '' ?>">
  </div>

  <div class="form-group">
    <label>Email</label>
    <input type="email" name="email" class="form-control" value="<?= $isEdit ? htmlspecialchars($contact['email']) : '' ?>">
  </div>

  <div class="form-group">
    <label>Phone</label>
    <input type="text" name="phone" class="form-control" value="<?= $isEdit ? htmlspecialchars($contact['phone']) : '' ?>">
  </div>

  <div class="form-group">
    <label>Position</label>
    <input type="text" name="position" class="form-control" value="<?= $isEdit ? htmlspecialchars($contact['position']) : '' ?>">
  </div>

  <div class="form-group">
    <label>Company</label>
    <select name="crm_company_id" class="form-control">
      <option value="">-- Select Company --</option>
      <?php
        $companies = $conn->query("SELECT id, name FROM crm_companies WHERE company_id = $company_id");
        foreach($companies as $row):
      ?>
        <option value="<?= $row['id'] ?>" <?= $isEdit && $contact['crm_company_id'] == $row['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($row['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="form-group">
    <label>Notes</label>
    <textarea name="notes" class="form-control"><?= $isEdit ? htmlspecialchars($contact['notes']) : '' ?></textarea>
  </div>

  <button type="submit" class="btn btn-success">Save</button>
  <a href="./" class="btn btn-default">Cancel</a>
</form>
