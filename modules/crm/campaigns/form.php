<form method="post" action="save.php">
  <input type="hidden" name="id" value="<?= $campaign['id'] ?? '' ?>">
  <?php if (isset($campaign['id'])): ?>
    <input type="hidden" name="id" value="<?= $campaign['id'] ?>">
  <?php endif; ?>

  <div class="form-group">
    <label>Campaign Name</label>
    <input type="text" name="campaign_name" class="form-control" value="<?= $campaign['campaign_name'] ?? '' ?>" required>
  </div>

  <div class="form-group">
    <label>Description</label>
    <textarea name="description" class="form-control"><?= $campaign['description'] ?? '' ?></textarea>
  </div>

  <div class="form-group">
    <label>Target Segment</label>
    <select name="target_segment_id" class="form-control" required>
      <?php
      $res = $conn->query("SELECT id, segment_name, target_type FROM crm_segments WHERE company_id = $company_id");
      while ($row = $res->fetch_assoc()):
      ?>
        <option value="<?= $row['id'] ?>" <?= ($campaign['target_segment_id'] ?? '') == $row['id'] ? 'selected' : '' ?>>
          <?= $row['segment_name'] ?> (<?= $row['target_type'] ?>)
        </option>
      <?php endwhile; ?>
    </select>
  </div>

  <div class="form-group">
    <label>Schedule Time</label>
    <input type="datetime-local" name="scheduled_at" class="form-control" value="<?= isset($campaign['scheduled_at']) ? date('Y-m-d\TH:i', strtotime($campaign['scheduled_at'])) : '' ?>">
  </div>

  <button type="submit" class="btn btn-primary">Save Campaign</button>
</form>
