<div class="modal fade" id="marcaModal" tabindex="-1" aria-labelledby="marcaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="marcaForm">
            <input type="hidden" id="marcaId" name="id" value="0">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="marcaModalLabel">Adicionar Marca</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="ativo" name="ativo" checked>
                        <label class="form-check-label" for="ativo">
                            Ativo
                        </label>
                    </div>
                </div>
                <button type="button" class="btn btn-success" id="loading" style="display:none;" disabled>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Processando...
                </button>                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="btnCloseMarca"
                        data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary" id="btnSalva">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>