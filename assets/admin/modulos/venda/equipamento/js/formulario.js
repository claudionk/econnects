$(document).ready(function(){

    $('#cnpj_cpf').on('blur',function() {
        busca_cliente();
        busca_cotacao_salva();
    });

});
