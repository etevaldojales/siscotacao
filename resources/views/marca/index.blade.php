@extends('adminlte::page')

@section('title', 'SIS-COTAÇÃO')

@section('content_header')
<h1 class="m-0 text-dark">Cadastro de Marca</h1>
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container">
        <button class="btn btn-primary mb-3" id="btnAddMarca">Adicionar Marca</button>
        <div class="mb-3">
            <input type="text" class="form-control" id="searchMarca" placeholder="Buscar marca..."
                onclick="this.value=''" style="width: 350px; float: left">
            <input type="button" class="btn btn-primary" value="Buscar Todos" onclick="pesquisarMarcas()"
                style="clear: both; margin-left: 8px;">
            <button type="button" class="btn btn-danger" id="loading" style="display:none;" disabled>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Processando...
            </button>
        </div>
        <table class="table table-bordered" id="marcaTable">
            <thead>
                <tr style="background-color: #D3D3D3">
                    <th>Nome</th>
                    <th>Ativo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $i = 0;
                ?>
                @if($marcas)
                    @foreach($marcas as $marca)
                        <tr data-id="{{ $marca->id }}" style="background-color: {{ $i % 2 == 0 ? '#FOF8FF' : '#DCDCDC' }}">
                            <td>{{ $marca->nome }}</td>
                            <td>{{ $marca->ativo ? 'Sim' : 'Não' }}</td>
                            <td>
                                <nobr>
                                    <button class="btn btn-sm btn-warning btnEdit">Editar</button>
                                    <button class="btn btn-sm btn-danger btnDelete">Excluir</button>
                                </nobr>
                            </td>
                        </tr>
                        <?php $i++; ?>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" style="text-align: center">Nenhuma marca cadastrada</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @include('marca.form')

@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/marca.js') }}"></script>
@stop
