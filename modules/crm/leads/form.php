<?php
$isEdit = isset($lead);
?>

<form method="POST" action="save.php">
  <?php if ($isEdit): ?>
    <input type="hidden" name="id" value="<?= $lead['id'] ?>">
  <?php endif; ?>

  <div class="form-group">
    <label>Full Name</label>
    <input type="text" name="full_name" class="form-control" value="<?= $isEdit ? htmlspecialchars($lead['full_name']) : '' ?>" required>
  </div>

  <div class="form-group">
    <label>Email</label>
    <input type="email" name="email" class="form-control" value="<?= $isEdit ? htmlspecialchars($lead['email']) : '' ?>">
  </div>

  <div class="form-group">
    <label>Phone</label>
    <input type="text" name="phone" class="form-control" value="<?= $isEdit ? htmlspecialchars($lead['phone']) : '' ?>">
  </div>

  <div class="form-group">
    <label>Job Title</label>
    <input type="text" name="job_title" class="form-control" value="<?= $isEdit ? htmlspecialchars($lead['job_title']) : '' ?>">
  </div>

  <div class="form-group">
    <label>Company Name</label>
    <input type="text" name="company_name" class="form-control" value="<?= $isEdit ? htmlspecialchars($lead['company_name']) : '' ?>">
  </div>

  <div class="form-group">
    <label>Source</label>
    <select name="source" class="form-control" required>
      <option value="web" <?= $isEdit && $lead['source'] == 'web' ? 'selected' : '' ?>>Web</option>
      <option value="email" <?= $isEdit && $lead['source'] == 'email' ? 'selected' : '' ?>>Email</option>
      <option value="social" <?= $isEdit && $lead['source'] == 'social' ? 'selected' : '' ?>>Social</option>
      <option value="manual" <?= $isEdit && $lead['source'] == 'manual' ? 'selected' : '' ?>>Manual</option>
    </select>
  </div>

  <div class="form-group">
    <label>Status</label>
    <select name="status" class="form-control">
      <?php foreach (['new', 'contacted', 'qualified', 'unqualified', 'converted'] as $status): ?>
        <option value="<?= $status ?>" <?= $isEdit && $lead['status'] == $status ? 'selected' : '' ?>>
          <?= ucfirst($status) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="form-group">
    <label>Notes</label>
    <textarea name="notes" class="form-control"><?= $isEdit ? htmlspecialchars($lead['notes']) : '' ?></textarea>
  </div>

  <?php //include '../_dms_attach.php'; ?>

  <button type="submit" class="btn btn-success">Save Lead</button>
  <a href="list.php" class="btn btn-default">Cancel</a>
</form>
