<?php
$isEdit = isset($company);
?>

<form method="POST" action="save.php">
  <?php if ($isEdit): ?>
    <input type="hidden" name="id" value="<?= $company['id'] ?>">
  <?php endif; ?>

  <div class="form-group">
    <label>Company Name</label>
    <input type="text" name="name" class="form-control" required value="<?= $isEdit ? htmlspecialchars($company['name']) : '' ?>">
  </div>

  <div class="form-group">
    <label>Industry</label>
    <input type="text" name="industry" class="form-control" value="<?= $isEdit ? htmlspecialchars($company['industry']) : '' ?>">
  </div>

  <div class="form-group">
    <label>Phone</label>
    <input type="text" name="phone" class="form-control" value="<?= $isEdit ? htmlspecialchars($company['phone']) : '' ?>">
  </div>

  <div class="form-group">
    <label>Website</label>
    <input type="text" name="website" class="form-control" value="<?= $isEdit ? htmlspecialchars($company['website']) : '' ?>">
  </div>

  <div class="form-group">
    <label>Notes</label>
    <textarea name="notes" class="form-control"><?= $isEdit ? htmlspecialchars($company['notes']) : '' ?></textarea>
  </div>

  <button type="submit" class="btn btn-success">Save</button>
  <a href="list.php" class="btn btn-default">Cancel</a>
</form>
