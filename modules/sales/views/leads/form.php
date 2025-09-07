<?php
$isEdit = isset($lead);
?>

<div class="col-8">
  <form method="POST" action="save.php">
    <input type="hidden" name="customer_type" value="lead">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><?= !$isEdit ? 'New' : ''; ?> Leads Details</h3>
      </div>

      <div class="card-body">
        <?php if ($isEdit): ?>
          <input type="hidden" name="id" value="<?= $lead['id'] ?>">
        <?php endif; ?>
      
        <div class="form-group mb-3">
          <label>Leads Title</label>
          <input type="text" name="title" class="form-control" value="<?= $isEdit ? htmlspecialchars($lead['title']) : '' ?>" required>
        </div>
      
        <div class="form-group mb-3">
          <label>Prospects Name</label>
          <input type="text" name="name" class="form-control" value="<?= $isEdit ? htmlspecialchars($lead['name']) : '' ?>" required>
        </div>
        
        <div class="row mb-3">
          <div class="col">
            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" class="form-control" value="<?= $isEdit ? htmlspecialchars($lead['email']) : '' ?>">
            </div>
          </div>
          <div class="col">
            <div class="form-group">
              <label>Phone</label>
              <input type="text" name="phone" class="form-control" value="<?= $isEdit ? htmlspecialchars($lead['phone']) : '' ?>">
            </div>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col">
            <div class="form-group">
              <label>Job Title</label>
              <input type="text" name="job_title" class="form-control" value="<?= $isEdit ? htmlspecialchars($lead['job_title']) : '' ?>">
            </div>
          </div>
          <div class="col">
            <div class="form-group">
              <label>Company Name</label>
              <input type="text" name="company_name" class="form-control" value="<?= $isEdit ? htmlspecialchars($lead['company_name']) : '' ?>">
            </div>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col">
            <div class="form-group">
              <label>Source</label>
              <select name="source" class="form-control" required>
                <?php foreach(['Web', 'Email', 'Phone', 'Referral', 'Event', 'social media', 'manual', 'Other'] as $source) { ?>
                <option value="web" <?= ($isEdit && $lead['source'] == $source) ? 'selected' : '' ?>><?= $source; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="col">
            <div class="form-group">
              <label>Status</label>
              <select name="status" class="form-control">
                <?php foreach(['new', 'contacted', 'qualified', 'unqualified', 'lost', 'won', 'closed', 'converted'] as $status) { ?>
                  <option value="<?= $status ?>" <?= $isEdit && $lead['status'] == $status ? 'selected' : '' ?>>
                    <?= ucwords($status) ?>
                  </option>
                <?php } ?>
              </select>
            </div>
          </div>
          <?php if($isEdit) { ?>
          <?php $permit_user = "assign"; if(in_array($_SESSION['role'], super_roles()) || in_array($permit_user, get_user_permissions($user_id))) { ?>
          <div class="col">
            <div class="form-group">
                <label>Assigned To (Sales Person)</label>
                <select name="assigned_to" class="form-control select2">
                    <?php if(in_array($_SESSION['user_role'], system_users())) {
                        $users = $conn->query("SELECT id, name FROM users");
                    } else { ?>
                    <?php $users = $conn->query("SELECT id, name FROM users WHERE company_id = $company_id AND role = 'sales'"); ?>
                    <?php } ?>
                    <?php foreach($users as $user) { ?>
                        <option value="<?= $user['id']; ?>" <?= ($user['id'] == $lead['assigned_to']) ? 'selected' : ''; ?>><?= $user['name']; ?></option>
                    <?php } ?>
                </select>
            </div>
          </div>
          <?php } }?>
        </div>

        <div class="form-group mb-3">
          <label>Notes</label>
          <textarea name="notes" id="summernote" class="form-control"><?= $isEdit ? htmlspecialchars($lead['notes']) : '' ?></textarea>
        </div>
  
        <?php //include '../_dms_attach.php'; ?>
      </div>

  
      <div class="card-footer">
        <div class="form-group float-end">
          <a href="./" class="btn btn-default">Cancel</a>
          <button type="submit" class="btn btn-success">Save Lead</button>
        </div>
      </div>
    </div>
  
  </form>
</div>
