@extends('adminlte::page')

@section('content')
    <div class="container">
        <h1>Permissões por Perfil</h1>

        <div class="mb-3">
            <a href="{{ route('permissions.create') }}" class="btn btn-success">Cadastrar Nova Permissão</a>
        </div>

        <div id="alertPlaceholder"></div>

        @foreach ($roles as $role)
            <div class="card mb-4">
                <div class="card-header">
                    <h3>{{ $role->name }}</h3>
                    <!--<p>{{ $role->description }}</p>-->
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('permissions.assign') }}">
                        @csrf
                        <input type="hidden" name="role_id" value="{{ $role->id }}">
                        <div class="mb-3">
                            <label>Permissões</label>
                            <div class="form-check">
                                @foreach ($role->permissions as $permission)
                                    <div>
                                        <input class="form-check-input" type="checkbox" name="permission_ids[]"
                                            value="{{ $permission->id }}" checked
                                            id="perm_{{ $permission->id }}_role_{{ $role->id }}">
                                        <label class="form-check-label" for="perm_{{ $permission->id }}_role_{{ $role->id }}">
                                            {{ App\Helpers\Helper::traduzirLabelPermission($permission->name) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="form-check">
                                @php
                                    $allPermissions = \App\Models\Permission::all();
                                    $rolePermissionIds = $role->permissions->pluck('id')->toArray();
                                    $unassignedPermissions = $allPermissions->whereNotIn('id', $rolePermissionIds);
                                @endphp
                                @foreach ($unassignedPermissions as $permission)
                                    <div>
                                        <input class="form-check-input" type="checkbox" name="permission_ids[]"
                                            value="{{ $permission->id }}" id="perm_{{ $permission->id }}_role_{{ $role->id }}">
                                        <label class="form-check-label" for="perm_{{ $permission->id }}_role_{{ $role->id }}">
                                            {{ App\Helpers\Helper::traduzirLabelPermission($permission->name) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Salvar Permissões</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endsection
@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // Existing JS for permission modal and CRUD can be removed or adapted as needed
    });
</script>