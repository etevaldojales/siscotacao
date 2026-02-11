@extends('adminlte::page')

@section('title', 'SIS-COTAÇÃO')

@section('content_header')
<h1 class="m-0 text-dark">Cadastro de Menu</h1>
@stop



@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container">

        <button class="btn btn-primary mb-3" id="addMenuBtn" style="float: left">Adicionar Menu</button>
        &nbsp;&nbsp;
        <button type="button" class="btn btn-success" id="loading" style="display:none;" disabled>
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Processando...
            </button>
            <ul id="menu-list" class="list-group" style="clear:both">
                <!-- Menu items will be appended here by jQuery -->
            </ul>
    </div>

    @include('menu.form')

@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/menu.js') }}"></script>

@stop