@extends('adminlte::page')

@section('content')
    <div class="container">
        <h1>Cadastrar Nova Permissão</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('save-permissions') }}">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Nome da Permissão</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Descrição</label>
                <textarea class="form-control" id="description" name="description">{{ old('description') }}</textarea>
            </div>
            <button type="button" class="btn btn-success" id="loading" style="display:none;" disabled>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Processando...
            </button>
            <button type="submit" class="btn btn-primary">Cadastrar</button>
            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
@endsection