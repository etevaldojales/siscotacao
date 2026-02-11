<div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="roleForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalLabel">Adicionar Perfil</h5>
                    <button type="button" id="btnClose" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="roleId" name="roleId" value="0">
                    <div class="mb-3">
                        <label for="roleName" class="form-label">Peril</label>
                        <input type="text" class="form-control" id="roleName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="roleDescription" class="form-label">Descrição</label>
                        <textarea class="form-control" id="roleDescription" name="description"></textarea>
                    </div>
                    <div id="formErrors" class="alert alert-danger d-none"></div>
                </div>
                <button type="button" class="btn btn-success" id="loading" style="display:none;" disabled>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Processando...
                </button>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="saveRoleBtn">Salvar</button>
                    <button type="button" id="btnCancel" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>