<form method="POST" action="save.php">
  <input type="hidden" name="id" value="<?= $rule['id'] ?? '' ?>">

  <div class="form-group">
    <label>Rule Name</label>
    <input type="text" name="rule_name" class="form-control" value="<?= htmlspecialchars($rule['rule_name'] ?? '') ?>" required>
  </div>

  <div class="form-group">
    <label>Trigger Type</label>
    <select name="trigger_type" class="form-control">
      <option value="contact_inactivity" <?= $rule['trigger_type'] === 'contact_inactivity' ? 'selected' : '' ?>>Contact Inactivity</option>
      <option value="deal_stage_change" <?= $rule['trigger_type'] === 'deal_stage_change' ? 'selected' : '' ?>>Deal Stage Change</option>
      <option value="campaign_sent" <?= $rule['trigger_type'] === 'campaign_sent' ? 'selected' : '' ?>>Campaign Sent</option>
    </select>
  </div>

  <div class="form-group">
    <label>Trigger Value</label>
    <input type="text" name="trigger_value" class="form-control" value="<?= htmlspecialchars($rule['trigger_value'] ?? '') ?>">
    <small>e.g., "7_days" or "stage:negotiation"</small>
  </div>

  <div class="form-group">
    <label>Action Type</label>
    <select name="action_type" class="form-control">
      <option value="create_task" <?= $rule['action_type'] === 'create_task' ? 'selected' : '' ?>>Create Task</option>
      <option value="send_email" <?= $rule['action_type'] === 'send_email' ? 'selected' : '' ?>>Send Email</option>
      <option value="create_reminder" <?= $rule['action_type'] === 'create_reminder' ? 'selected' : '' ?>>Create Reminder</option>
    </select>
  </div>

  <div class="form-group">
    <label>Action Value (JSON)</label>
    <textarea name="action_value" class="form-control" rows="3"><?= htmlspecialchars($rule['action_value'] ?? '') ?></textarea>
  </div>

  <div class="form-group">
    <label>Status</label>
    <select name="is_active" class="form-control">
      <option value="1" <?= $rule['is_active'] ? 'selected' : '' ?>>Active</option>
      <option value="0" <?= !$rule['is_active'] ? 'selected' : '' ?>>Inactive</option>
    </select>
  </div>

  <button type="submit" class="btn btn-success">Save Rule</button>
  <a href="list.php" class="btn btn-default">Cancel</a>
</form>
