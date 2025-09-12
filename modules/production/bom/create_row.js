function addRow() {
    const row = `<tr>
        <td><input type="text" name="material[]" class="form-control" required></td>
        <td><input type="number" name="material_qty[]" class="form-control" step="0.01" required></td>
        <td><input type="text" name="material_uom[]" class="form-control" required></td>
        <td><button type="button" onclick="this.closest('tr').remove()" class="btn btn-danger btn-sm">Remove</button></td>
    </tr>`;
    document.querySelector('#materials-table tbody').insertAdjacentHTML('beforeend', row);
}