<?php
$isEdit = isset($activity);
?>

<form method="POST" action="save.php" class="col-lg-8">
  <?php if ($isEdit): ?>
    <input type="hidden" name="id" value="<?= $activity['id'] ?>">
  <?php endif; ?>

  <div class="form-group">
    <label>Type</label>
    <select name="type" class="form-control" required>
      <option value="call" <?= $isEdit && $activity['type'] == 'call' ? 'selected' : '' ?>>Call</option>
      <option value="meeting" <?= $isEdit && $activity['type'] == 'meeting' ? 'selected' : '' ?>>Meeting</option>
      <option value="email" <?= $isEdit && $activity['type'] == 'email' ? 'selected' : '' ?>>Email</option>
      <option value="note" <?= $isEdit && $activity['type'] == 'note' ? 'selected' : '' ?>>Note</option>
    </select>
  </div>

  <div class="form-group">
    <label>Subject</label>
    <input type="text" name="subject" class="form-control" required value="<?= $isEdit ? htmlspecialchars($activity['subject']) : '' ?>">
  </div>

  <div class="form-group">
    <label>Due Date</label>
    <input type="datetime-local" name="due_date" class="form-control" value="<?= $isEdit ? date('Y-m-d\TH:i', strtotime($activity['due_date'])) : '' ?>">
  </div>

  <div class="form-group">
    <label>Reminder At</label>
    <input type="datetime-local" name="reminder_at" class="form-control" value="<?= $isEdit && $activity['reminder_at'] ? date('Y-m-d\TH:i', strtotime($activity['reminder_at'])) : '' ?>">
  </div>

  <div class="form-group">
    <label>Related To</label>
    <div class="row">
      <div class="col-sm-6">
        <select name="related_type" class="form-control">
          <option value="contact" <?= $isEdit && $activity['related_type'] == 'contact' ? 'selected' : '' ?>>Contact</option>
          <option value="opportunity" <?= $isEdit && $activity['related_type'] == 'opportunity' ? 'selected' : '' ?>>Opportunity</option>
          <option value="lead" <?= $isEdit && $activity['related_type'] == 'lead' ? 'selected' : '' ?>>Lead</option>
        </select>
      </div>
      <div class="col-sm-6">
        <input type="number" name="related_id" class="form-control" placeholder="Related Record ID" value="<?= $isEdit ? $activity['related_id'] : '' ?>">
      </div>
    </div>
  </div>

  <div class="form-group">
    <label>Assigned To (User ID)</label>
    <select name="assigned_to" class="form-control" required>
      <option value="" selected>-- Select User --</option>
      <?php $users = $conn->query("SELECT * FROM users WHERE company_id = {$_SESSION['company_id']} AND status = 'active'"); ?>
      <?php foreach($users as $user) { ?>
        <option value="<?= $user['id'] ?>" <?= $isEdit && $activity['assigned_to'] == $user['id'] ? 'selected' : '' ?>><?= $user['name'] ?></option>
      <?php } ?>
    </select>
  </div>

  <div class="form-group">
    <label>Status</label>
    <select name="status" class="form-control">
      <option value="pending" <?= $isEdit && $activity['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
      <option value="done" <?= $isEdit && $activity['status'] == 'done' ? 'selected' : '' ?>>Done</option>
    </select>
  </div>

  <div class="form-group">
    <label>Notes</label>
    <textarea name="notes" class="form-control"><?= $isEdit ? htmlspecialchars($activity['notes']) : '' ?></textarea>
  </div>

  <button type="submit" class="btn btn-success">Save</button>
  <a href="list.php" class="btn btn-default">Cancel</a>
</form>
