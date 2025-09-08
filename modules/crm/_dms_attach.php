<?php
$docs = $conn->query("SELECT id, title FROM documents ORDER BY created_at DESC");
?>
<div class="form-group">
    <label>Attach Documents</label>
    <select name="doc_ids[]" class="form-control" multiple>
        <?php foreach ($docs as $d): ?>
            <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['title']) ?></option>
        <?php endforeach; ?>
    </select>
    <small class="text-muted">Hold Ctrl or Shift to select multiple</small>
</div>