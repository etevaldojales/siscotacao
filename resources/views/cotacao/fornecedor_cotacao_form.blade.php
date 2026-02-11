<div class="modal fade" id="fornecedorCotacaoModal" tabindex="-1" aria-labelledby="fornecedorCotacaoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="fornecedorCotacaoForm">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="fornecedorCotacaoModalLabel">Adicionar Fornecedor Cotação</h5>
          <button type="button" id="btnCloseFornecedorCot" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">X</button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="fornecedorCotacaoId" name="id" value="0" />
          <input type="hidden" id="fornecedorCotacaoCotacaoId" name="cotacao_id" value="0" />
          <input type="hidden" id="fornecedorCotacaoItemId" name="item_id" value="0" />
          <div class="row">
            <div class="mb-3 col-md-6">
              <label for="fornecedor_id" class="form-label">Fornecedor</label>
              <select class="form-control" id="fornecedor_id" name="fornecedor_id" required>
                <option value="">Selecione o fornecedor</option>
                @foreach(App\Models\Fornecedor::all() as $fornecedor)
                  <option value="{{ $fornecedor->id }}">{{ $fornecedor->nome }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3 col-md-3">
              <label for="valor_unitario" class="form-label">Valor Unitário</label>
              <input type="number" step="0.01" class="form-control" id="valor_unitario" name="valor_unitario" required />
            </div>
            <div class="mb-3 col-md-3">
              <label for="valor_total" class="form-label">Valor Total</label>
              <input type="number" step="0.01" class="form-control" id="valor_total" name="valor_total" readonly />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" id="btnSaveFornecedorCot">Salvar</button>
          <button type="button" id="btnCancelFornecedorCot" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
        <button type="button" class="btn btn-success" id="loadingFornecedorCotModal" disabled style="display: none;">
          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          Processando...
        </button>
        <br>
        <div class="row" id="fornecedorCotacaoTable"></div>
      </div>
    </form>
  </div>
</div>
