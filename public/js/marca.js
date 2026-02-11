$(document).ready(function () {
    var marcaModal = new bootstrap.Modal(
        document.getElementById("marcaModal")
    );

    $("#btnAddMarca").click(function () {
        $("#marcaForm")[0].reset();
        $("#marcaId").val("0");
        $("#marcaModalLabel").text("Adicionar Marca");
        marcaModal.show();
    });

    $("#marcaTable").on("click", ".btnEdit", function () {
        var row = $(this).closest("tr");
        $("#marcaId").val(row.data("id"));
        $.ajax({
            type: "POST",
            url: "carrega-marca",
            data: { id: row.data("id") },
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                $("#nome").val(data.nome);
                $("#ativo").prop('checked', data.ativo == 1);
                $("#marcaModalLabel").text("Editar Marca");
                marcaModal.show();
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                for (i in XMLHttpRequest) {
                    if (i != "channel")
                        console.log(i + " : " + XMLHttpRequest[i]);
                }
            },
        });
    });

    $("#marcaTable").on("click", ".btnDelete", function () {
        if (!confirm("Tem certeza que deseja excluir esta marca?")) {
            return;
        }
        var row = $(this).closest("tr");
        var id = row.data("id");

        $.ajax({
            url: "remover-marca",
            type: "POST",
            data: { id: id },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                row.remove();
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                for (i in XMLHttpRequest) {
                    if (i != "channel")
                        console.log(i + " : " + XMLHttpRequest[i]);
                }
            },
        });
    });

    $("#marcaForm").submit(function (e) {
        e.preventDefault();
        $("#loading").show();
        $("#btnSalva").prop('disabled', true);
        var id = $("#marcaId").val();
        var url = "salvar-marca";
        var type = "POST";

        $.ajax({
            url: url,
            type: type,
            dataType: "json",
            data: {
                id: id,
                nome: $("#nome").val(),
                ativo: $("#ativo").is(":checked") ? 1 : 0,
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                $("#btnSalva").prop('disabled', false);
                $("#loading").hide();
                location.reload();
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                for (i in XMLHttpRequest) {
                    if (i != "channel")
                        console.log(i + " : " + XMLHttpRequest[i]);
                }
            },
        });
    });

    $("#btnCloseMarca").on("click", function () {
        marcaModal.hide();
    });

    $("#btnCancelMarca").on("click", function () {
        marcaModal.hide();
    });

    // Ajax search for marcas
    $("#searchMarca").on("input", function () {
        var query = $(this).val();
        $("#loading").show();

        $.ajax({
            url: "marcas-search",
            type: "post",
            data: { query: query },
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                $("#loading").hide();
                var tbody = $("#marcaTable tbody");
                tbody.empty();

                if (data.length === 0) {
                    tbody.append(
                        '<tr><td colspan="3" class="text-center">Nenhuma marca encontrada.</td></tr>'
                    );
                } else {
                    $.each(data, function (index, marca) {
                        var rowColor = index % 2 === 0 ? "#FOF8FF" : "#DCDCDC";
                        var row =
                            '<tr data-id="' +
                            marca.id +
                            '" style="background-color: ' +
                            rowColor +
                            '">' +
                            "<td>" +
                            marca.nome +
                            "</td>" +
                            "<td>" +
                            (marca.ativo == 1 ? "Sim" : "Não") +
                            "</td>" +
                            "<td>" +
                            '<nobr><button class="btn btn-sm btn-warning btnEdit">Editar</button> ' +
                            '<button class="btn btn-sm btn-danger btnDelete">Excluir</button></nobr>' +
                            "</td>" +
                            "</tr>";
                        tbody.append(row);
                    });
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                for (i in XMLHttpRequest) {
                    if (i != "channel")
                        console.log(i + " : " + XMLHttpRequest[i]);
                }
            },
        });
    });
});

async function pesquisarMarcas() {
    $("#searchMarca").val("");
    $("#loading").show();
    $.ajax({
        url: "marcas-search",
        type: "POST",
        data: { query: "" },
        headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
        dataType: "json",
        success: function (data) {
            $("#loading").hide();
            var tbody = $("#marcaTable tbody");
            tbody.empty();

            if (data.length === 0) {
                tbody.append(
                    '<tr><td colspan="3" class="text-center">Nenhuma marca encontrada.</td></tr>'
                );
            } else {
                $.each(data, function (index, marca) {
                    var rowColor = index % 2 === 0 ? "#FOF8FF" : "#DCDCDC";
                    var row =
                        '<tr data-id="' +
                        marca.id +
                        '" style="background-color: ' +
                        rowColor +
                        '">' +
                        "<td>" +
                        marca.nome +
                        "</td>" +
                        "<td>" +
                        (marca.ativo == 1 ? "Sim" : "Não") +
                        "</td>" +
                        "<td>" +
                        '<nobr><button class="btn btn-sm btn-warning btnEdit">Editar</button> ' +
                        '<button class="btn btn-sm btn-danger btnDelete">Excluir</button></nobr>' +
                        "</td>" +
                        "</tr>";
                    tbody.append(row);
                });
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            for (i in XMLHttpRequest) {
                if (i != "channel") console.log(i + " : " + XMLHttpRequest[i]);
            }
        },
    });
}
