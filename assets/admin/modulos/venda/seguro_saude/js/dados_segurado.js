$(document).ready(function(){



    $('.inputmask-date').datepicker({autoclose: true, todayHighlight: true, format: "dd/mm/yyyy", endDate: '0d', language: 'pt-BR', forceParse : false});

    //verifica se é uma edição ou POST

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
