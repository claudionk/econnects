function populaSelectCategoria(id){
    $.ajax({
        url: base_url + "admin/equipamento/service_categorias/" + id,
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

function populaSelectMarca(id){
    $.ajax({
        url: base_url + "admin/equipamento/service_marcas/" + id,
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
// encodeURIComponent
function populaSelectModelo(id){
    $.ajax({
        url: base_url + "admin/equipamento/service/" + encodeURI(id),
        type: "GET",
        dataType: "json",
        success: function(data){
            console.log('retornou', data.items);

            $(".js-equipamento_id-ajax").select2("trigger", "select", {
                data: data.items
            });
        },
        error: function(error){
            console.log("Error:", error);
        }
    });
}

$(document).ready(function(){

    $('#cnpj_cpf').on('blur',function() {
        busca_cliente();
    });

    $('#ean').on('blur',function() {
        buscaDadosEAN();
    });

    //busca produtos conforme o codigo da tabela do cliente selecionado
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
        populaSelectCategoria($(".js-categorias-ajax").data('selected'));
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
        populaSelectMarca($(".js-equipamento_marca_id-ajax").data('selected'));
    }

    //busca produtos conforme o codigo da tabela do cliente selecionado
    $(".js-equipamento_id-ajax").select2({
        ajax: {
            url: base_url + "admin/equipamento/service",
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
        templateSelection: formatRepoSelectionEquip, // omitted for brevity, see the source of this page*/
        language: "pt-BR"
    });

    //verifica se é uma edição ou POST
    if($(".js-equipamento_id-ajax").data('selected') != ''){
        populaSelectModelo($(".js-equipamento_id-ajax").data('selected'));
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

function formatRepoSelectionEquip (repo) {
    var nome = repo.nome || repo.ean;

    if (typeof nome != 'undefined') {
        $('#ean').val(repo.ean);
        $('#equipamento_nome').val(nome);

        populaSelectCategoria(repo.equipamento_categoria_id);
        populaSelectMarca(repo.equipamento_marca_id);

    }
    return nome;
}

function formatRepoEquipamento (repo) {
    if (repo.loading) return repo.text;

    var markup = "<div class='select2-result-repository clearfix'>" +
        // "<div class='select2-result-repository__avatar'></div>" +
        "<div class='select2-result-repository__meta'>" +
        "<div class='select2-result-repository__title'>Equipamento: " + repo.nome + "<br/><span class='text-bold'>EAN:"+ repo.ean +"</span></div>";

    if (repo.descricao) {
        markup += "<div class='select2-result-repository__description'>" + repo.descricao + "</div>";
    }

    markup += "<div class='select2-result-repository__statistics'>" +
        "<div class='select2-result-repository__forks'><i class='fa fa-map-marker'></i> <span class='text-bold'>MARCA: </span>" + repo.equipamento_marca_nome + "</div>" +
        "<div class='select2-result-repository__forks'><i class='fa fa-folder-open'></i> <span class='text-bold'>LINHA / CATEGORIA: </span>" + repo.equipamento_categoria_nome + " - " + repo.equipamento_categoria_codigo + "</div>" +
        "<div class='select2-result-repository__forks'><i class='fa fa-folder-open'></i> <span class='text-tags'>TAGS: </span>" + repo.tags + "</div>" +
        "</div>" +
//        "<div class='select2-result-repository__statistics'>" +
//        "<div class='select2-result-repository__forks'><i class='fa fa-anchor'></i><span class='text-bold'> Peso: </span>" + repo.peso + "</div>" +
//        "<div class='select2-result-repository__forks'><i class='fa fa-home'></i><span class='text-bold'> IPI:</span> " + repo.ipi + "</div>" +
//        "<div class='select2-result-repository__forks'><i class='fa fa-flash'></i><span class='text-bold'> Tensão:</span> " + repo.voltagem + "</div>" +
//        "<div class='select2-result-repository__forks'><i class='fa fa-dot-circle-o'></i><span class='text-bold'> MONO/TRI:</span> " + repo.tipo_voltagem + "</div>" +
//        "<div class='select2-result-repository__forks'><i class='fa fa-sellsy'></i><span class='text-bold'> Aplicação:</span> " + repo.aplicacao + "</div>" +
//        "</div>" +
        "</div></div>";

    return markup;
}
