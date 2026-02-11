@extends('adminlte::page')

@section('content')
    <style>
        /* Highlight class for selected row */
        .highlight-row {
            background-color: #d1ecf1 !important; /* Light blue background */
        }
    </style>

    <div class="row" style="margin-top: 2%">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="mb-0" style="font-size: 2rem;">Retorno Cotações</p>
                    <div class="form-group mt-3" id="cotacoes" style="width: 100%;float: left">
                    </div>
                    <div class="form-group mt-3" style="width: 350px; margin-left: 30%; margin-top: 10%; clear: both;">
                        <button type="button" class="btn btn-success" id="loading" style="margin-top: 33px; display: none;" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Processando...
                        </button>
                    </div>
                    <div class="form-group mt-3 grid_cotacoes"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Fornecedor Details -->
    <div class="modal fade" id="fornecedorModal" tabindex="-1" role="dialog" aria-labelledby="fornecedorModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document" style="max-width: 900px;">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="fornecedorModalLabel">Detalhes do Fornecedor</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div id="fornecedorDetails">
              <p><strong>Razão Social:</strong> <span id="fornecedorRazaoSocial"></span></p>
              <p><strong>CNPJ:</strong> <span id="fornecedorCNPJ"></span></p>
              <p><strong>Telefone:</strong> <span id="fornecedorTelefone"></span></p>
              <p><strong>Email:</strong> <span id="fornecedorEmail"></span></p>
              <!-- Add more fornecedor fields as needed -->
              <p><strong>Forma de Pagamento: </strong> <span id="formaPagamento"></span></p>
              <p><strong>Prazo de Entrega: </strong> <span id="prazoEntrega"></span></p>
            </div>
            <hr>
            <h5>Itens da Cotação</h5>
            <div id="fornecedorCotacoesGrid"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Justificativa Modal -->
    <div class="modal fade" id="justificativaModal" tabindex="-1" role="dialog" aria-labelledby="justificativaModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document" style="max-width: 600px;">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="justificativaModalLabel">Justificativa</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="justificativaForm">
              <div class="form-group">
                <label for="selectJustificativa">Por favor, selecione a justificativa para aprovar este item:</label>
                <select class="form-control" id="selectJustificativa" required>
                  <option value="">Selecione uma justificativa</option>
                </select>
              </div>
              <input type="hidden" id="justificativaItemIndex" />
              <input type="hidden" id="justificativaFornecedorId" />
              <input type="hidden" id="justificativaCotacaoId" />
              <input type="hidden" id="justificativaItemId" />
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="btnSubmitJustificativa">Enviar Justificativa</button>
          </div>
        </div>
      </div>
    </div>

    @section('js')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
        <script src="{{ asset('js/home.js') }}"></script>
    @endsection
@endsection
