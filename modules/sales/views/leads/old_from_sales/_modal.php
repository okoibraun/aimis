<!-- Add Lead Modal -->
<div class="modal fade" id="leadModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="../../controllers/leads.php" class="modal-content">
      <input type="hidden" name="action" value="add">
      <div class="modal-header">
        <h5 class="modal-title">Add Lead</h5>
        <button class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Customer ID</label>
          <input name="customer_id" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Title</label>
          <input name="title" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Date</label>
          <input type="date" name="lead_date" class="form-control">
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status" class="form-control">
            <option>New</option>
            <option>Contacted</option>
            <option>Qualified</option>
            <option>Lost</option>
            <option>Won</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success">Save</button>
      </div>
    </form>
  </div>
</div>