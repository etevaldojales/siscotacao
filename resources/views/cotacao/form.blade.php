<div class="modal fade" id="cotacaoModal" tabindex="-1" aria-labelledby="cotacaoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="cotacaoForm">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="cotacaoModalLabel">Adicionar Cotação</h5>
          <button type="button" id="btnCloseCot" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">X</button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="cotacaoId" name="id" value="0" />
          <div class="row">
            <div class="mb-3 col-md-6">
              <label for="numero" class="form-label">Número</label>
              <input type="number" class="form-control" id="numero" name="numero" required readonly/>
              <script>
                window.loggedInUserId = {{ Auth::user()->id }};
              </script>
            </div>
            <div class="mb-3 col-md-6">
              <label for="inicio" class="form-label">Início</label>
              <input type="datetime-local" class="form-control" id="inicio" name="inicio" />
            </div>
            <div class="mb-3 col-md-6">
              <label for="encerramento" class="form-label">Encerramento</label>
              <input type="datetime-local" class="form-control" id="encerramento" name="encerramento" required />
            </div>
          </div>
          <div class="row">
            <div class="mb-3 col-md-6">
              <label for="status" class="form-label">Status</label>
              <select class="form-control" id="status" name="status" required>
                <option value="1">Em aberto</option>
                <option value="2">Programado</option>
                <option value="3">Encerrado</option>
                <option value="4">Cancelado</option>
                <option value="5">Finalizado</option>
                <option value="6">Aprovado</option>
              </select>
            </div>
            <div class="mb-3 col-md-6">
              <label for="status_envio" class="form-label">Status Envio</label>
              <select class="form-control" id="status_envio" name="status_envio" required>
                <option value="1">Não enviada</option>
                <option value="2">Enviada</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="mb-3 col-md-12">
              <label for="descricao" class="form-label">Descrição</label>
              <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
            </div>
          </div>
          <div class="row">
            <div class="mb-3 col-md-12">
              <label for="observacao" class="form-label">Observação</label>
              <textarea class="form-control" id="observacao" name="observacao" rows="3"></textarea>
            </div>
          </div>
          <div class="row">
            <div class="mb-3 col-md-12">
              <label for="endereco_entrega" class="form-label">Endereço de Entrega</label>
              <input type="text" class="form-control" id="endereco_entrega" name="endereco_entrega" />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="btnCancelCot" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary" id="btnSaveCotacao">Salvar</button>
        </div>
        <button type="button" class="btn btn-success" id="loadingModal" disabled style="display: none;">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Processando...
        </button>
        <br>
      </div>
    </form>
  </div>
</div>
