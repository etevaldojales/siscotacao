@extends('adminlte::page')



@section('title', 'SIS-COTAÇÃO')



@section('content_header')

<h1 class="m-0 text-dark">Cadastro de Categoria</h1>



@stop



@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}">



    <div class="container">

        <button class="btn btn-primary mb-3" id="btnAddCategoria">Adicionar Categoria</button>

        <table class="table table-bordered" id="categoriaTable">

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Nome</th>

                    <th>Descrição</th>

                    <th>CNPJ Comprador</th>

                    <th>Ações</th>

                </tr>

            </thead>

            <tbody>

                @foreach($categorias as $categoria)

                    <tr data-id="{{ $categoria->id }}">

                        <td>{{ $categoria->id }}</td>

                        <td>{{ $categoria->nome }}</td>

                        <td>{{ $categoria->descricao }}</td>

                        <td>{{ $categoria->cnpj_comprador }}</td>

                        <td>

                            <button class="btn btn-sm btn-warning btnEdit">Editar</button>

                            @if(auth()->user()->isAdmin())

                            <button class="btn btn-sm btn-danger btnDelete">Excluir</button>

                            @endif

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>



    @include('categoria.form')



@endsection



@section('js')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="{{ asset('js/categoria.js') }}"></script>



@stop