$(document).ready(function(){



    $('#cnpj_cpf').on('blur',function() {
        busca_cliente();
    });

    $('#data_saida').datepicker({autoclose: true, todayHighlight: true, format: "dd/mm/yyyy", startDate: '0d', language: 'pt-BR', forceParse : false});
    $('#data_retorno').datepicker({autoclose: true, todayHighlight: true, format: "dd/mm/yyyy", startDate: '0d', language: 'pt-BR', forceParse : false});

    $('#origem_id').select2({
        formatNoMatches: function () {
            return "A pesquisa não retornou nenhum resultado";
        }
    });


    $('#destino_id').select2({
        formatNoMatches: function () {
            return "A pesquisa não retornou nenhum resultado";
        }
    });

});