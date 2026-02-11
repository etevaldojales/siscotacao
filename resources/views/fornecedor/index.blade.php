@extends('adminlte::page')

@section('title', 'SIS-COTAÇÃO')

@section('content_header')
<h1 class="m-0 text-dark">Cadastro de Fornecedor</h1>
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container">
        <button class="btn btn-primary mb-3" id="btnAddFornecedor">Adicionar Fornecedor</button>
        <div class="mb-3">
            <input type="text" class="form-control" id="searchFornecedor" placeholder="Buscar fornecedor..."
                onclick="this.value=''" style="width: 350px; float: left">
            <input type="button" class="btn btn-primary" value="Buscar Todos" onclick="pesquisarFornecedores()"
                style="clear: both; margin-left: 8px;">
            <button type="button" class="btn btn-success" id="loading" style="display:none;" disabled>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Processando...
            </button>
        </div>
        <table class="table table-bordered" id="fornecedorTable">
            <thead>
                <tr style="background-color: #D3D3D3">
                    <th>CNPJ</th>
                    <th>Razão Social</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Celular</th>
                    <th>Tipo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 0;
                ?>
                @if(count($fornecedores) > 0)
                    @foreach($fornecedores as $fornecedor)

                            <tr data-id="{{ $fornecedor->id }}" style="background-color: {{ $i % 2 == 0 ? '#FOF8FF' : '#DCDCDC' }}">
                                <td>{{ $fornecedor->cnpj }}</td>
                                <td>{{ $fornecedor->razao_social }}</td>
                                <td>{{ $fornecedor->email }}</td>
                                <td>{{ $fornecedor->telefone }}</td>
                                <td>{{ $fornecedor->celular }}</td>
                                <td>{{ $fornecedor->tipo }}</td>
                                <td>
                                    <nobr>
                                        <button class="btn btn-sm btn-warning btnEdit">Editar</button>
                                        <button class="btn btn-sm btn-danger btnDelete">Excluir</button>
                                    </nobr>
                                </td>
                            </tr>
                            <?php
                            $i++;
                            ?>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" style="text-align: center">Nenhum fornecedor cadastrado</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @include('fornecedor.form')

@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="{{ asset('js/fornecedor.js') }}"></script>
<script>
    $('#cnpj').mask('00.000.000/0000-00', { reverse: true });
    $('#cnpj_matriz').mask('00.000.000/0000-00', { reverse: true });
    $('#email').mask("A", {
        translation: {
            "A": { pattern: /[\w@\-.+]/, recursive: true }
        }
    });
    $('#cep').mask('00.000-000', { reverse: true });
    $('#telefone').mask('(00) 00000-0000');
    $('#celular').mask('(00) 00000-0000');
    $('#whatsapp').mask('(00) 00000-0000');


</script>

@stop