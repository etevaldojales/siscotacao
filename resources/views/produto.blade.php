@extends('adminlte::page')

@section('title', 'SIS-COTAÇÃO')

@section('content_header')
<h1 class="m-0 text-dark">Cadastro de Produto</h1>
@stop
@section('css')
<link rel="stylesheet" href="{{asset('css/app.css')}}">
@stop
@section('content')
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
    integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

<div class="container mt-5">

    <form id="frmproduto" method="post">
        @csrf

        <div class="form-group">
            <label for="codigo">Código <span style="color: red;">*</span></label>
            <input type="number" class="form-control" id="codigo" name="codigo" placeholder="Digite o código do produto" min="0" max="99999999" required>
        </div>
        <div class="form-group">
            <label for="name">Nome do Produto <span style="color: red;">*</span></label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Digite o nome do produto"
                required>
        </div>
        <div class="form-group">
            <label for="description">Descrição</label>
            <textarea class="form-control" id="description" name="description" rows="3"
                placeholder="Digite a descrição do produto"></textarea>
        </div>
        <!--
        <div class="form-group">
            <label for="price">Preço <span style="color: red;">*</span></label>
            <input type="text" class="form-control" id="price" name="price" placeholder="Digite o preço" required>
        </div>
        <div class="form-group">
            <label for="stock">Estoque <span style="color: red;">*</span></label>
            <input type="number" class="form-control" id="stock" name="stock"
                placeholder="Digite a quantidade em estoque" required>
        </div>
        -->
        <div class="form-group">
            <label for="status">Status <span style="color: red;">*</span></label>
            <select class="form-control" id="status" name="status" required>
                <option value="Ativo">Ativo</option>
                <option value="Inativo">Inativo</option>
            </select>
        </div>
        <div class="form-group">
            <label for="category_id">Categoria <span style="color: red;">*</span></label>
            <select class="form-control" id="category_id" name="category_id" required>
                <option value="">Selecione a categoria</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="user_id">Usuário <span style="color: red;">*</span></label>
            <input type="hidden" id="user_id" name="user_id" value="{{ auth()->id() }}">
            <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
        </div>
        <div class="form-group">
            <label for="marcas">Marcas</label>
            <select multiple class="form-control" id="marcas" name="marcas[]">
                @foreach($marcas as $marca)
                    <option value="{{ $marca->id }}">{{ $marca->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="cnpj_comprador">CNPJ Comprador <span style="color: red;">*</span></label>
            <input type="text" class="form-control" id="cnpj_comprador_display" name="cnpj_comprador_display" required> 
            <input type="hidden" id="cnpj_comprador" name="cnpj_comprador" value="">
        </div>
        <button type="submit" id="btnsalva" class="btn btn-primary">Salvar</button>
        <button type="reset" class="btn btn-warning">Cancelar</button>
        <button type="button" class="btn btn-success" id="loading" style="display:none;" disabled>
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Processando...
        </button>
        <input type="hidden" id="price" name="price" value="0">
        <input type="hidden" id="stock" name="stock" value="0">
    </form>

    <div class="alert alert-success mt-3" id="sucesso" style="display:none;">
        Operação realizada com sucesso!
    </div>
    <div class="alert alert-danger mt-3" id="erro" style="display:none;">
        Falha ao realizar a operação. Tente novamente.
    </div>

    <!-- Product Grid -->
    <div class="mt-5">
        <h3>Produtos Cadastrados</h3>

        <!-- Search Form -->
        <form id="searchForm" method="GET" action="{{ route('produto') }}" class="form-inline mb-3">
            <input type="text" name="search" id="searchInput" class="form-control mr-2" placeholder="Buscar produto..."
                value="{{ isset($search) ? $search : '' }}" onclick="this.value=''">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <a href="javascript: void(0)" onclick="allProducts(1);" class="btn btn-secondary ml-2">Limpar</a>
        </form>

        <div id="productTableContainer">
            @include('partials.product_table', ['produtos' => $produtos])
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    $(document).ready(function () {
        // Apply currency mask to price input
        $('#price').mask('000.000.000.000.000,00', { reverse: true });
        $('#cnpj_comprador').mask('00.000.000/0000-00', { reverse: true });

        var editMode = false;
        var editProductId = 0;

        function appendProductToGrid(produto) {
            var newRow = '<tr>' +
                '<td>' + produto.name + '</td>' +
                '<td>' + (produto.description ? produto.description : '') + '</td>' +
                '<td>' + parseFloat(produto.price).toFixed(2).replace('.', ',') + '</td>' +
                '<td>' + produto.stock + '</td>' +
                '<td>' + produto.status + '</td>' +
                '<td>' + (produto.user ? produto.user.name : '') + '</td>' +
                '</tr>';
            $('#productGrid tbody').append(newRow);
            setTimeout('remove_msg()', 1000);
        }

        $("#frmproduto").submit(function (e) {
            e.preventDefault();

            // Simple validation
            var codigo = $("#codigo").val().trim();
            var name = $("#name").val().trim();
            var price = $("#price").val().trim();
            var stock = $("#stock").val().trim();
            var status = $("#status").val();
            var user_id = $("#user_id").val();
            var category_id = $("#category_id").val();
            var marcas = $("#marcas").val();
            var cnpj_comprador = $("#cnpj_comprador").val().trim() !== '' ? $("#cnpj_comprador").val().trim() : $("#cnpj_comprador_display").val().trim();
            var id = editProductId;

            if (codigo === "" || name === "" || status === "" || cnpj_comprador === "") {
                alert("Por favor, preencha todos os campos obrigatórios.");
                return;
            }

            // Convert masked price to plain number with dot as decimal separator
            var numericPrice = price.replace(/\./g, '').replace(',', '.');

            $("#btnsalva").prop("disabled", true);
            $("#loading").show();
            $("#sucesso").hide();
            $("#erro").hide();

            var formData = {
                codigo: codigo,
                name: name,
                description: $("#description").val().trim(),
                price: numericPrice,
                stock: stock,
                status: status,
                user_id: user_id,
                id: id,
                category_id: category_id,
                marcas: marcas,
                cnpj_comprador: cnpj_comprador,
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            var ajaxOptions = {
                data: formData,
                success: function (response) {
                    $("#loading").hide();
                    $("#btnsalva").prop("disabled", false);
                    $("#sucesso").show();
                    $("#frmproduto")[0].reset();
                    editMode = false;
                    editProductId = 0;
                    $("#btnsalva").text('Salvar');

                    // Refresh product grid
                    fetchProducts(1);
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    for (i in XMLHttpRequest) {
                        if (i != "channel")
                            console.log(i + " : " + XMLHttpRequest[i]);
                    }
                },
            };

            if (editMode && editProductId) {
                $('#id').val(editProductId);
                ajaxOptions.url = "{{ route('salvar-produto') }}";
                ajaxOptions.type = "POST";
            } else {
                ajaxOptions.url = "{{ route('salvar-produto') }}";
                ajaxOptions.type = "POST";
            }

            $.ajax(ajaxOptions);
        });

        // Edit button click handler
        $(document).on('click', '.btn-edit', function () {
            var button = $(this);
            editMode = true;
            editProductId = button.data('id');

            $("#codigo").val(button.data('codigo'));
            $("#name").val(button.data('name'));
            $("#description").val(button.data('description'));
            //$("#price").val(button.data('price').toString().replace('.', ','));
            //$("#stock").val(button.data('stock'));
            $("#status").val(button.data('status'));
            $("#user_id").val(button.data('user_id'));
            $("#category_id").val(button.data('category_id'));
            $("#cnpj_comprador").val(button.data('cnpj_comprador'));
            $("#cnpj_comprador_display").val(button.data('cnpj_comprador_display'));
            $("#marcas").val(button.data('marcas') ? button.data('marcas').split(',') : []);
            $("#btnsalva").text('Atualizar');
        });

        // New JS for loading comprador by CNPJ
        $('#cnpj_comprador').on('keypress', function (e) {
            if (e.which == 13) { // Enter key pressed
                $('#loading').show();
                e.preventDefault();
                var cnpj = $(this).val().replace(/\D/g, '');
                if (cnpj.length === 14) {
                    
                    $('#cnpjError').hide();
                    $.ajax({
                        url: "{{ route('check.cnpj') }}",
                        type: 'POST',
                        data: {
                            cnpj: cnpj,
                            user_id: $('#user_id').val(),
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            $('#loading').hide();
                            if (response && response.user) {
                                var userId = response.user.id;
                                var userCnpj = response.user.cnpj;
                                // Select the comprador in the cnpj_comprador select
                                $('#cnpj_comprador').val(userCnpj);
                                $('#cnpjError').hide();
                            } else {
                                $('#loading').hide();
                                $('#comprador_id').val('');
                                $('#cnpjError').show();
                            }
                        },
                        error: function () {
                            $('#loading').hide();
                            $('#comprador_id').val('');
                            $('#cnpjError').show();
                        }
                    });
                } else {
                    $('#loading').hide();
                    $('#cnpj_comprador').val('');
                    $('#cnpjError').show();
                }
            }
        });

        // Delete button click handler
        $(document).on('click', '.btn-delete', function () {
            if (!confirm('Tem certeza que deseja excluir este produto?')) {
                return;
            }

            var productId = $(this).data('id');

            $("#loading").show();
            $.ajax({
                url: "{{ route('remover-produto') }}",
                type: "POST",
                data: {
                    id: productId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $("#loading").hide();
                    $("#sucesso").show();
                    // Refresh product grid
                    fetchProducts(1);
                },
                error: function (xhr, status, error) {
                    $("#loading").hide();
                    $("#erro").show();
                    console.error(error);
                }
            });
        });

        async function remove_msg() {
            $("#sucesso").hide();
            $("#erro").hide();
        }

        // AJAX search and pagination
        $('#searchForm').on('submit', function (e) {
            e.preventDefault();
            fetchProducts(1);
        });

        $(document).on('click', '#productTableContainer .pagination a', function (e) {
            e.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            fetchProducts(page);
        });

        function fetchProducts(page) {
            var search = $('#searchInput').val();
            $("#loading").show();
            $.ajax({
                url: "{{ route('produto') }}",
                type: 'GET',
                data: { search: search, page: page },
                dataType: 'json',
                success: function (data) {
                    $("#loading").hide();
                    remove_msg();
                    $('#productTableContainer').html(data.html);
                },
                error: function (xhr, status, error) {
                    $("#loading").hide();
                    console.error('Error fetching products:', error);
                }
            });
        }
    });

    async function allProducts(page) {
        $('#searchInput').val('');
        var search = $('#searchInput').val();
        $("#loading").show();
        $.ajax({
            url: "{{ route('produto') }}",
            type: 'GET',
            data: { search: search, page: page },
            dataType: 'json',
            success: function (data) {
                $("#loading").hide();
                $('#productTableContainer').html(data.html);
            },
            error: function (xhr, status, error) {
                $("#loading").hide();
                console.error('Error fetching products:', error);
            }
        });
    }

</script>

@stop