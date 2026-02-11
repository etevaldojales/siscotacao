@extends('adminlte::page')

@section('content')
    <div class="container mt-4">
        <h2>Justificativas</h2>
        <button class="btn btn-primary mb-3" id="btnAddJustificativa">Adicionar Justificativa</button>
        <button type="button" class="btn btn-success" id="loading" style="display:none; margin-left: 2%; margin-top: -12px" disabled>
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Processando...
        </button>        
        <table class="table table-bordered" id="justificativaTable"></table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="justificativaModal" tabindex="-1" role="dialog" aria-labelledby="justificativaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width: 600px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="justificativaModalLabel">Nova Justificativa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="justificativaForm">
                        @csrf
                        <div class="form-group">
                            <label for="selectJustificativa">Justificativa</label>
                            <select class="form-control" id="selectJustificativa" required>
                                <option value="">Selecione uma justificativa</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" required>
                                <option value="1">Ativo</option>
                                <option value="0">Inativo</option>
                            </select>
                        </div>
                        <input type="hidden" id="justificativaId" value="0" />
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnSaveJustificativa">Salvar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        var justificativaModal = new bootstrap.Modal(document.getElementById('justificativaModal'));
        $(document).ready(function () {
            

            function loadJustificativas() {
                $('#loading').show();
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: '{{ route('load.justificativas') }}',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (r) {
                        var rows = '';
                        rows += '       <thead>';
                        rows += '           <tr>';
                        rows += '               <th>ID</th>';
                        rows += '               <th>Descrição</th>';
                        rows += '               <th>Status</th>';
                        rows += '               <th>Ações</th>';
                        rows += '           </tr>';
                        rows += '       </thead>';
                        rows += '       <tbody>';
                        $.each(r, function (i, item) {
                            var statusText = item.status == 1 ? 'Ativo' : 'Inativo';
                            rows += '<tr>' +
                                '<td>' + item.id + '</td>' +
                                '<td>' + item.descricao + '</td>' +
                                '<td>' + statusText + '</td>' +
                                '<td>' +
                                '<button class="btn btn-sm btn-info btn-edit" data-id="' + item.id + '">Editar</button> ' +
                                '<button class="btn btn-sm btn-danger btn-delete" data-id="' + item.id + '">Excluir</button>' +
                                '</td>' +
                                '</tr>';
                        });
                        rows += '       </tbody>';
                        $('#justificativaTable').html(rows);
                        $('#loading').hide();

                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        $('#loading').hide();
                        $('loading').hide();
                        for (i in XMLHttpRequest) {
                            if (i != "channel")
                                console.log(i + " : " + XMLHttpRequest[i])
                        }
                    }
                });

            }

            loadJustificativas();

            $('#btnAddJustificativa').click(function () {
                $('#justificativaId').val('');
                $('#descricao').val('');
                $('#status').val('1');
                justificativaModal.show();
            });

            $('#btnSaveJustificativa').click(function () {
                $('#loading').show();
                var id = $('#justificativaId').val();
                var descricao = $('#descricao').val();
                var status = $('#status').val();

                if (!descricao) {
                    alert('Descrição é obrigatória.');
                    return;
                }

                var data = {
                    descricao: descricao,
                    status: status,
                    id: id
                };

                $.ajax({
                    url: '{{ route('save.justificativa') }}',
                    type: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        $('#loading').hide();
                        justificativaModal.hide();
                        loadJustificativas();
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        $('#loading').hide();
                        alert('Erro ao salvar justificativa.');
                        for (i in XMLHttpRequest) {
                            if (i != "channel")
                                console.log(i + " : " + XMLHttpRequest[i])
                        }
                    }
                });

            });

            $('#justificativaTable').on('click', '.btn-edit', function () {
                $('#loading').show();
                var id = $(this).data('id');
                $.ajax({
                    url: '{{ route('justificativa.get') }}',
                    type: 'POST',
                    data: { id: id },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        $('#loading').hide();
                        $('#justificativaId').val(data.id);
                        $('#descricao').val(data.descricao);
                        $('#status').val(data.status);
                        justificativaModal.show();
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        $('#loading').hide();
                        alert('Erro ao carregar justificativa.');
                        for (i in XMLHttpRequest) {
                            if (i != "channel")
                                console.log(i + " : " + XMLHttpRequest[i])
                        }
                    }
                });
            });

            $('#justificativaTable').on('click', '.btn-delete', function () {
                if (!confirm('Tem certeza que deseja excluir esta justificativa?')) {
                    return;
                }
                $('#loading').show();
                var id = $(this).data('id');
                $.ajax({
                    url: '{{ route('justificativa.delete') }}',
                    type: 'POST',
                    data: { id: id },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        $('loading').hide();
                        loadJustificativas();
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        $('#loading').hide();
                        alert('Erro ao excluir justificativa.');
                        
                        for (i in XMLHttpRequest) {
                            if (i != "channel")
                                console.log(i + " : " + XMLHttpRequest[i])
                        }
                    }
                });
            });
        });
    </script>
@endsection