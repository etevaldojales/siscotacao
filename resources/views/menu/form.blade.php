
<!-- Modal -->
<div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="menuForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="menuModalLabel">Add/Edit Menu</h5>
          <button type="button" class="btn-close" id="btnClose" data-bs-dismiss="modal" aria-label="Close">X</button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="menuId" name="id" value="0">
          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <input type="text" class="form-control" id="description" name="description" required>
          </div>
          <div class="mb-3">
            <label for="icon" class="form-label">Icon</label>
            <input type="text" class="form-control" id="icon" name="icon" placeholder="e.g. fa fa-home">
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="actived" name="actived" value="1" checked>
            <label class="form-check-label" for="actived">
              Active
            </label>
          </div>
        </div>
        <button type="button" class="btn btn-success" id="loading" style="display:none;" disabled>
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Processando...
        </button>        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" id="btnCancel" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="saveMenuBtn">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>

