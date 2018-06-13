$(document).ready(function(){



    $('.inputmask-date').datepicker({autoclose: true, todayHighlight: true, format: "dd/mm/yyyy", endDate: '0d', language: 'pt-BR', forceParse : false});

    $(".js-categorias-ajax").select2({
        ajax: {
            url: base_url + "admin/equipamento/service_categorias",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true

        },
        escapeMarkup: function (markup) { return markup ; }, // let our custom formatter work
        minimumInputLength: 1,
        templateResult: formatRepoCategoriasEquipamento, // omitted for brevity, see the source of this page*/
        templateSelection: formatRepoSelectionEquipamento, // omitted for brevity, see the source of this page*/
        language: "pt-BR"
    });

    //verifica se é uma edição ou POST
    if($(".js-categorias-ajax").data('selected') != ''){
        console.log($(".js-categorias-ajax").data('selected'))

        $.ajax({
            url: base_url + "admin/equipamento/service_categorias/" + $(".js-categorias-ajax").data('selected'),
            type: "GET",
            dataType: "json",
            success: function(data){
                console.log('retornou', data.items);

                $(".js-categorias-ajax").select2("trigger", "select", {
                    data: data.items
                });
            },
            error: function(error){
                console.log("Error:", error);
            }
        });
    }

    //busca produtos conforme o codigo da tabela do cliente selecionado
    $(".js-equipamento_marca_id-ajax").select2({
        ajax: {
            url: base_url + "admin/equipamento/service_marcas",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, params)
            {
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true

        },
        escapeMarkup: function (markup) { return markup ; }, // let our custom formatter work
        minimumInputLength: 1,
        templateResult: formatRepoCategoriasEquipamento, // omitted for brevity, see the source of this page*/
        templateSelection: formatRepoSelectionEquipamento, // omitted for brevity, see the source of this page*/
        language: "pt-BR"
    });

    //verifica se é uma edição ou POST
    if($(".js-equipamento_marca_id-ajax").data('selected') != ''){
        console.log('teste', $(".js-categorias-ajax").data('selected'))

        $.ajax({
            url: base_url + "admin/equipamento/service_marcas/" + $(".js-equipamento_marca_id-ajax").data('selected'),
            type: "GET",
            dataType: "json",
            success: function(data){
                console.log('retornou', data.items);

                $(".js-equipamento_marca_id-ajax").select2("trigger", "select", {
                    data: data.items
                });
            },
            error: function(error){
                console.log("Error:", error);
            }
        });
    }


});



function formatRepoCategoriasEquipamento (repo) {
    if (repo.loading) return repo.text;

    var markup = "<div class='select2-result-repository clearfix'>" +
        "<div class='select2-result-repository__meta'>" +
        "<div class='select2-result-repository__title'>" + repo.nome + "<br/></div>";

    return markup;
}

function formatRepoSelectionEquipamento (repo) {
    return repo.nome || repo.ean;
}