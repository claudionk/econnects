$(document).ready(function() {

    $('#filter_parceiro_id').select2();
    $('#filter_data_inicio').datepicker({autoclose: true, todayHighlight: true, format: "dd/mm/yyyy", startDate: '-120d', language: 'pt-BR', forceParse : false});
    $('#filter_data_fim').datepicker({autoclose: true, todayHighlight: true, format: "dd/mm/yyyy", endDate: '0d', language: 'pt-BR', forceParse : false});

    $('.btn-export').on('click',function(){
        console.log('export');
        $('.form-cobranca').attr('target', '_blank');
        $('.form-cobranca').attr('action', $('#url_excel').val());
        $('.form-cobranca').submit();
        $('.form-cobranca').attr('target', '_self');
        $('.form-cobranca').attr('action', '');
    });

});