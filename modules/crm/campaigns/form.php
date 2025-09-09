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
    <select name="target_segment_id" id="selectTarget"  class="form-control" required>
      <?php
        $res = $conn->query("SELECT id, segment_name, target_type FROM crm_segments WHERE company_id = $company_id");
        foreach($res as $row) {
      ?>
        <option value="<?= $row['id'] ?>" data-targettype="<?= $row['target_type'] ?>" <?= ($campaign['target_segment_id'] ?? '') == $row['id'] ? 'selected' : '' ?>>
          <?= $row['segment_name'] ?> (<?= $row['target_type'] ?>)
        </option>
      <?php } ?>
    </select>
  </div>

  <input type="hidden" name="target_type" id="targetType">

  <div class="row">
    <div class="col">
      <div class="form-group">
        <label>Schedule Time</label>
        <input type="datetime-local" name="scheduled_at" class="form-control" value="<?= isset($campaign['scheduled_at']) ? date('Y-m-d\TH:i', strtotime($campaign['scheduled_at'])) : '' ?>">
      </div>
    </div>
    <?php if(isset($id)) { ?>
    <div class="col">
      <div class="form-group">
        <label>Status</label>
        <select name="status" id="" class="form-control">
          <?php foreach(['draft','active','completed','archived'] as $status) { ?>
          <option value="<?= $status ?>" <?= isset($campaign['id']) && $campaign['status'] == $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
          <?php } ?>
        </select>
      </div>
    </div>
    <?php } ?>
  </div>
  

  <div class="form-group float-end mt-3">
    <a href="./" class="btn btn-default">Cancel</a>
    <button type="submit" class="btn btn-primary">Save Campaign</button>
  </div>
</form>

<script>
  const selectTarget = document.querySelector("#selectTarget");

  selectTarget.addEventListener('change', () => {
    document.querySelector('#targetType').value = selectTarget.options[selectTarget.selectedIndex].dataset.targettype;
  });
</script>
