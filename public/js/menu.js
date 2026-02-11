$(document).ready(function () {
    var menuModal = new bootstrap.Modal(document.getElementById("menuModal"));
    var isEdit = false;
    var menu = [];

    function fetchMenu() {
        $("#loading").show();
        $.ajax({
            url: "menu/data",
            method: "GET",
            dataType: "json",
            success: function (data) {
                //console.log(data);
                $("#menu-list").empty();
                if (data.length === 0) {
                    $("#menu-list").append(
                        '<li class="list-group-item">No menu items found.</li>'
                    );
                } else {
                    var x = 0;
                    var cor;
                    $.each(data, function (index, item) {
                        cor = x % 2 == 0 ? "#D3D3D3" : "#F0F8FF";
                        var iconHtml = item.icon
                            ? '<i class="' + item.icon + '"></i> '
                            : "";
                        var activeClass = item.actived
                            ? "list-group-item-success"
                            : "list-group-item-secondary";
                        var listItem =
                            '<li class="list-group-item d-flex justify-content-between align-items-center ' +
                            activeClass +
                            '" data-id="' +
                            item.id +
                            '" style="background-color: ' +
                            cor +
                            '">' +
                            "<span>" +
                            iconHtml +
                            item.description +
                            "</span>" +
                            "<span>" +
                            '<button class="btn btn-sm btn-info editMenuBtn me-2">Edit</button>' +
                            '<button class="btn btn-sm btn-danger deleteMenuBtn" style="margin-left: 8px;">Delete</button>' +
                            "</span>" +
                            "</li>";
                        $("#menu-list").append(listItem);
                        x++;
                    });
                    $("#loading").hide();
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                $("#menu-list")
                    .empty()
                    .append(
                        '<li class="list-group-item list-group-item-danger">Falha ao carregar menus.</li>'
                );

                for (i in XMLHttpRequest) {
                    if (i != "channel")
                        console.log(i + " : " + XMLHttpRequest[i]);
                }
            },            
        });
    }

    $("#addMenuBtn").click(function () {
        isEdit = false;
        $("#menuForm")[0].reset();
        $("#menuId").val("");
        $("#menuModalLabel").text("Adicionar Menu");
        $("#actived").prop("checked", true);
        menuModal.show();
    });

    $(document).on("click", ".editMenuBtn", function () {
        isEdit = true;
        var li = $(this).closest("li");
        var id = li.data("id");
        //console.log('ID: ' + id)
        $.ajax({
            url: "menu/data/especifico",
            method: "POST",
            data: {id: id},
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },            
            success: function (menu) {
                //console.log(menu);
                //menu = data;
                if (menu) {
                    $("#menuId").val(menu.id);
                    $("#description").val(menu.description);
                    $("#icon").val(menu.icon);
                    $("#actived").prop("checked", menu.actived);
                    $("#menuModalLabel").text("Edit Menu");
                    menuModal.show();
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                $("#menu-list")
                    .empty()
                    .append(
                        '<li class="list-group-item list-group-item-danger">Falha ao carregar menus.</li>'
                );

                for (i in XMLHttpRequest) {
                    if (i != "channel")
                        console.log(i + " : " + XMLHttpRequest[i]);
                }
            },            
        });
    });

    $("#menuForm").submit(function (e) {
        e.preventDefault();
        $("#loading").show();
        var id = $("#menuId").val();

        $.ajax({
            url: "salvar-menu",
            method: "post",
            dataType: "json",
            cache: false,
            data: {
                description: $("#description").val(),
                icon: $("#icon").val(),
                actived: $("#actived").is(":checked") ? 1 : 0,
                id: id,
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                console.log('Retorno: ' + response);
                $("#loading").hide();
                alert(response.message);
                menuModal.hide();
                fetchMenu();
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                for (i in XMLHttpRequest) {
                    if (i != "channel")
                        console.log(i + " : " + XMLHttpRequest[i]);
                }
            },
        });
    });

    $(document).on("click", ".deleteMenuBtn", function () {
        if (!confirm("Deseja realmente excluir esse Menu?")) return;

        var li = $(this).closest("li");
        var id = li.data("id");

        $.ajax({
            url: "remover-menu",
            method: "POST",
            data: {
                id: id,
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                alert(response.message);
                fetchMenu();
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                for (i in XMLHttpRequest) {
                    if (i != "channel")
                        console.log(i + " : " + XMLHttpRequest[i]);
                }
            },
        });
    });

    $("#btnClose").on("click", function () {
        menuModal.hide();
    });

    $("#btnCancel").on("click", function () {
        menuModal.hide();
    });

    fetchMenu();
});
