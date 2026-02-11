@extends('adminlte::page')

@section('title', 'SIS-COTAÇÃO')

@section('content_header')
<h1 class="m-0 text-dark">Cadastro de Perfil</h1>
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}" id="csrf-token">
    <div class="container">
        <button class="btn btn-primary mb-3" id="btnAddRole">Adicionar Perfis</button>

        <div id="alertPlaceholder"></div>

        <table class="table table-bordered" id="rolesTable">
            <thead class="thead-light">
                <tr>
                    <th>Perfil</th>
                    <th>Descrição</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                    <tr data-id="{{ $role->id }}">
                        <td>{{ $role->name }}</td>
                        <td>{{ $role->description }}</td>
                        <td>
                            <button class="btn btn-sm btn-info btnEditRole">Editar</button>
                            @if (auth()->user()->isAdmin())
                            <button class="btn btn-sm btn-danger btnDeleteRole">Excluir</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal for Add/Edit Role -->
    @include('roles.form')

@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
    var roleModal = new bootstrap.Modal(document.getElementById("roleModal"));

    // Show modal for adding role
    $("#btnAddRole").click(function () {
        $("#roleForm")[0].reset();
        $("#roleId").val("");
        $("#roleModalLabel").text("Aicionar Perfil");
        $("#formErrors").addClass("d-none").empty();
        roleModal.show();
    });

    // Show modal for editing role
    $("#rolesTable").on("click", ".btnEditRole", function () {
        var row = $(this).closest("tr");
        var id = row.data("id");
        var name = row.find("td:eq(0)").text();
        var description = row.find("td:eq(1)").text();

        $("#roleId").val(id);
        $("#roleName").val(name);
        $("#roleDescription").val(description);
        $("#roleModalLabel").text("Editar Perfil");
        $("#formErrors").addClass("d-none").empty();
        roleModal.show();
    });

    // Submit form for add/edit role
    $("#roleForm").submit(function (e) {
        e.preventDefault();
        $("#loading").show();

        var id = $("#roleId").val();
        var url = "save-roles";
        var method = "POST";

        $.ajax({
            url: url,
            method: method,
            dataType: "json",
            cache: false,

            data: {
                name: $("#roleName").val(),
                description: $("#roleDescription").val(),
                _token: "{{ csrf_token() }}",
                id: id,
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                $("#loading").hide();
                console.log("Sucesso: " + response);
                location.reload();
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                for (i in XMLHttpRequest) {
                    if (i != "channel")
                        console.log(i + " : " + XMLHttpRequest[i]);
                }
            },
            /*            
            error: function (xhr) {
                $("#loading").hide();

                var errors = xhr.responseJSON.errors;
                console.log("Erro:" + errors);
                var errorHtml = "<ul>";
                $.each(errors, function (key, value) {
                    errorHtml += "<li>" + value[0] + "</li>";
                });
                errorHtml += "</ul>";
                $("#formErrors").removeClass("d-none").html(errorHtml);
            },*/
        });
    });

    // Delete role
    $("#rolesTable").on("click", ".btnDeleteRole", function () {
        if (!confirm("Are you sure you want to delete this role?")) return;

        var row = $(this).closest("tr");
        var id = row.data("id");

        $.ajax({
            url: "/admin/roles/" + id,
            method: "DELETE",
            data: {
                _token: "{{ csrf_token() }}",
            },
            success: function (response) {
                location.reload();
            },
            error: function (xhr) {
                alert("Failed to delete role.");
            },
        });
    });

    $("#btnClose").on("click", function () {
        roleModal.hide();
    });

    $("#btnCancel").on("click", function () {
        roleModal.hide();
    });
});

</script>

@stop