$(document).ready(function(){

    $('#cnpj_cpf').on('blur',function() {
        busca_cliente();
    });

    //busca produtos conforme o codigo da tabela do cliente selecionado

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


