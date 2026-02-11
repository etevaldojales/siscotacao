@extends('adminlte::page')

@section('title', 'SIS-COTAÇÃO')

@section('content_header')
<h1 class="m-0 text-dark">Cadastro de Cotação</h1>
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container">
        <button class="btn btn-primary mb-3" id="btnAddCotacao">Adicionar Cotação</button>
        <div class="mb-3">
            <input type="text" class="form-control" id="searchCotacao" placeholder="Buscar cotação..."
                onclick="this.value=''" style="width: 350px; float: left">
            <input type="button" class="btn btn-primary" value="Buscar Todos" onclick="pesquisarCotacoes()"
                style="clear: both; margin-left: 8px;">
            <button type="button" class="btn btn-success" id="loading" style="display:none;" disabled>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Processando...
            </button>
        </div>
        <table class="table table-bordered" id="cotacaoTable">
            <thead>
                <tr style="background-color: #D3D3D3">
                    <th>Número</th>
                    <th>Encerramento</th>
                    <th>Status</th>
                    <th>Status Envio</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <div id="table-container">
                @include('cotacao.partials.cotacao_table')
            </div>
        </table>
        <div id="pagination-container">
            @include('cotacao.partials.cotacao_pagination')
        </div>
    </div>

    @include('cotacao.form')
    @include('cotacao.itens_cotacao_form')
    @include('cotacao.fornecedor_cotacao_form')

@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="{{ asset('js/cotacao.js') }}"></script>
@stop
