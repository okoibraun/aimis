<div class="modal fade" id="productModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="../../controllers/products.php" method="POST" class="modal-content">
      <input type="hidden" name="action" value="add">
      <div class="modal-header">
        <h5 class="modal-title">Add Product</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Name</label>
          <input name="name" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Price</label>
          <input type="number" step="0.01" name="price" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Discount Type</label>
          <select name="discount_type" class="form-control">
            <option value="none">None</option>
            <option value="percentage">Percentage</option>
            <option value="fixed">Fixed</option>
          </select>
        </div>
        <div class="form-group">
          <label>Discount Value</label>
          <input type="number" step="0.01" name="discount_value" class="form-control">
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
