<form method="post" action="save.php">
  <input type="hidden" name="id" value="<?= $segment['id'] ?? '' ?>">
  <?php if (isset($segment['id'])): ?>
    <input type="hidden" name="id" value="<?= $segment['id'] ?>">
  <?php endif; ?>

  <div class="form-group">
    <label>Segment Name</label>
    <input type="text" name="segment_name" class="form-control" value="<?= $segment['segment_name'] ?? '' ?>" required>
  </div>

  <div class="form-group">
    <label>Target Type</label>
    <select name="target_type" class="form-control" required>
      <?php foreach(['lead', 'customer'] as $target_type) { ?>
      <option value="<?= $target_type; ?>" <?= ($segment['target_type'] ?? '') === $target_type ? 'selected' : '' ?>><?= ucfirst($target_type); ?>s</option>
      <?php } ?>
    </select>
  </div>

  <div class="form-group">
    <label>Tag Filter</label>
    <input type="text" name="tag" class="form-control" value="<?= $filters['tag'] ?? '' ?>" placeholder="e.g. vip, newsletter">
  </div>

  <div class="form-group">
    <label>Status Filter</label>
    <input type="text" name="status" class="form-control" value="<?= $filters['status'] ?? '' ?>" placeholder="e.g. new, hot, customer">
  </div>

  <div class="form-group">
    <label>Location Filter</label>
    <input type="text" name="location" class="form-control" value="<?= $filters['location'] ?? '' ?>">
  </div>

  <button type="submit" class="btn btn-primary">Save Segment</button>
</form>
