<?php
$isEdit = isset($activity);
?>

<form method="POST" action="save.php" class="col">
  <?php if ($isEdit): ?>
    <input type="hidden" name="id" value="<?= $activity['id'] ?>">
  <?php endif; ?>

  <div class="form-group">
    <label>Type</label>
    <select name="type" class="form-control" required>
      <?php foreach(['call', 'meeting', 'email', 'task'] as $type) { ?>
      <option value="<?= $type; ?>" <?= $isEdit && $activity['type'] == $type ? 'selected' : '' ?>><?= ucfirst($type); ?></option>
      <?php } ?>
    </select>
  </div>

  <div class="form-group">
    <label>Subject</label>
    <input type="text" name="subject" class="form-control" required value="<?= $isEdit ? htmlspecialchars($activity['subject']) : '' ?>">
  </div>

  <div class="row">
    <div class="col">
      <div class="form-group">
        <label>Due Date</label>
        <input type="datetime-local" name="due_date" class="form-control" value="<?= $isEdit ? date('Y-m-d\TH:i', strtotime($activity['due_date'])) : '' ?>">
      </div>
    </div>
    <div class="col">
      <div class="form-group">
        <label>Reminder At</label>
        <input type="datetime-local" name="reminder_at" class="form-control" value="<?= $isEdit && $activity['reminder_at'] ? date('Y-m-d\TH:i', strtotime($activity['reminder_at'])) : '' ?>">
      </div>
    </div>
  </div>

  <div class="form-group">
    <div class="row">
      <div class="col-sm-6">
        <label>Related Type</label>
        <select name="related_type" class="form-control" id="relatedType">
          <option>-- Select --</option>
          <?php foreach(['customer', 'lead'] as $related_to) { ?>
          <option value="<?= $related_to; ?>" <?= $isEdit && $activity['related_type'] == $related_to ? 'selected' : '' ?>><?= ucfirst($related_to); ?></option>
          <?php } ?>
        </select>
      </div>
      <div class="col-sm-6" id="">
        <label>Related To</label>
        <div class="form-group" id="showRelatedTo">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col">
      <div class="form-group">
        <label>Assigned To (Sales Dept)</label>
        <select name="assigned_to" class="form-control">
          <option value="" selected>-- Select User --</option>
          <?php $users = $conn->query("SELECT * FROM users WHERE company_id = $company_id AND status = 'active' AND role = 'sales'"); ?>
          <?php foreach($users as $user) { ?>
            <option value="<?= $user['id'] ?>" <?= $isEdit && $activity['assigned_to'] == $user['id'] ? 'selected' : '' ?>><?= $user['name'] ?></option>
          <?php } ?>
        </select>
      </div>
    </div>
    <div class="col">
      <div class="form-group">
        <label>Status</label>
        <select name="status" class="form-control">
          <?php foreach(['pending', 'completed', 'cancelled'] as $status) { ?>
          <option value="<?= $status; ?>" <?= $isEdit && $activity['status'] == $status ? 'selected' : '' ?>><?= ucfirst($status); ?></option>
          <?php } ?>
        </select>
      </div>
    </div>
  </div>



  <div class="form-group mt-3">
    <label>Description</label>
    <textarea name="description" id="summernote" class="form-control"><?= $isEdit ? htmlspecialchars($activity['description']) : '' ?></textarea>
  </div>

  <div class="form-group mt-3 float-end">
    <a href="./" class="btn btn-default">Cancel</a>
    <button type="submit" class="btn btn-success">Save Activity</button>
  </div>
</form>

<script>
  const relatedType = document.querySelector('#relatedType');
  const showRelatedTo = document.querySelector('#showRelatedTo');

  relatedType.addEventListener('change', () => {
    if(relatedType.value == 'lead') {
      showRelatedTo.innerHTML = `
        <select name="related_id" class="form-control select2">
          <?php foreach($conn->query("SELECT * FROM sales_customers WHERE company_id = $company_id AND customer_type = 'lead'") as $lead) { ?>
          <option value="<?= $lead['id'] ?>" <?= $isEdit && $activity['related_id'] == $lead['id'] ? 'selected' : '' ?>><?= ucfirst($lead['title']) ?></option>
          <?php } ?>
        </select>
      `;
    } else if(relatedType.value == 'customer') {
      showRelatedTo.innerHTML = `
        <select name="related_id" class="form-control select2">
          <?php foreach($conn->query("SELECT * FROM sales_customers WHERE company_id = $company_id AND customer_type = 'customer'") as $customer) { ?>
            <option value="<?= $customer['id'] ?>" <?= $isEdit && $activity['related_id'] == $customer['id'] ? 'selected' : '' ?>><?= ucfirst($customer['name']) ?></option>
          <?php } ?>
        </select>
      `;
    } else {
      showRelatedTo.innerHTML = "";
    }
  });

  <?php if($isEdit) { ?>
  if(relatedType.value == 'lead') {
    showRelatedTo.innerHTML = `
      <select name="related_id" class="form-control select2">
        <?php foreach($conn->query("SELECT * FROM sales_customers WHERE company_id = $company_id AND customer_type = 'lead'") as $lead) { ?>
        <option value="<?= $lead['id'] ?>" <?= $isEdit && $activity['related_id'] == $lead['id'] ? 'selected' : '' ?>><?= ucfirst($lead['title']) ?></option>
        <?php } ?>
      </select>
    `;
  } else if(relatedType.value == 'customer') {
    showRelatedTo.innerHTML = `
      <select name="related_id" class="form-control select2">
        <?php foreach($conn->query("SELECT * FROM sales_customers WHERE company_id = $company_id AND customer_type = 'customer'") as $customer) { ?>
          <option value="<?= $customer['id'] ?>" <?= $isEdit && $activity['related_id'] == $customer['id'] ? 'selected' : '' ?>><?= ucfirst($customer['name']) ?></option>
        <?php } ?>
      </select>
    `;
  }
  <?php } ?>
</script>
