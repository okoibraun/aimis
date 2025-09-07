<div class="modal fade" id="customerModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="../../controllers/customers.php" method="POST" class="modal-content">
      <input type="hidden" name="action" value="add">
      <div class="modal-header">
        <h5 class="modal-title">Add Customer</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Company Name</label>
          <input name="company_name" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" class="form-control">
        </div>
        <div class="form-group">
          <label>Phone</label>
          <input name="phone" class="form-control">
        </div>
        <div class="form-group">
          <label>Country</label>
          <input name="country" class="form-control">
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="is_active" class="form-control">
            <option value="1" selected>Active</option>
            <option value="0">Inactive</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success">Save</button>
      </div>
    </form>
  </div>
</div>