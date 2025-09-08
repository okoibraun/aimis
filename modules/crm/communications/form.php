<?php
$isEdit = isset($comm);
?>
<form method="POST" action="save.php">
  <?php if ($isEdit): ?>
    <input type="hidden" name="id" value="<?= $comm['id'] ?>">
  <?php endif; ?>
  <input type="hidden" name="related_type" value="<?= $_GET['related_type'] ?>">
  <input type="hidden" name="related_id" value="<?= $_GET['id'] ?>">

  <div class="form-group">
    <label>Type</label>
    <select name="communication_type" class="form-control" required>
      <option value="call">Call</option>
      <option value="email">Email</option>
      <option value="meeting">Meeting</option>
      <option value="note">Note</option>
    </select>
  </div>

  <div class="form-group">
    <label>Subject</label>
    <input type="text" name="subject" class="form-control" required value="<?= $isEdit ? htmlspecialchars($comm['subject']) : '' ?>">
  </div>

  <div class="form-group">
    <label>Details</label>
    <textarea name="details" class="form-control"><?= $isEdit ? htmlspecialchars($comm['details']) : '' ?></textarea>
  </div>

  <button type="submit" class="btn btn-primary">Save</button>
</form>
