var cartao;
var cartao_debito;

$(document).ready(function() {

    $('#validade').datepicker({autoclose: true, todayHighlight: true, format: "mm/yyyy", startDate: '0d', language: 'pt-BR', forceParse : false,  minViewMode: 1});
    $('#validade_debito').datepicker({autoclose: true, todayHighlight: true, format: "mm/yyyy", startDate: '0d', language: 'pt-BR', forceParse : false,  minViewMode: 1});


    cartao = $('#validateSubmitForm #pagamento-credito').card({
        // a selector or DOM element for the form where users will
        // be entering their information
        /*form: 'validateSubmitForm', */ // *required*
        // a selector or DOM element for the container
        // where you want the card to appear
        container: '.card-wrapper', // *required*

        formSelectors: {
            numberInput: 'input[name="numero"]', // optional — default input[name="number"]
            expiryInput: 'input[name="validade"]', // optional — default input[name="expiry"]
            cvcInput: 'input[name="codigo"]', // optional — default input[name="cvc"]
            nameInput: 'input[name="nome_cartao"]' // optional - defaults input[name="name"]
        },
        width: '280px', // optional — default 280px
        formatting: true,

        // Strings for translation - optional
        messages: {
            validDate: 'Validade', // optional - default 'valid\nthru'
            monthYear: 'mm/aaaa', // optional - default 'month/year'
        },

        // Default placeholders for rendered fields - optional
        placeholders: {
            number: '•••• •••• •••• ••••',
            name: 'Nome Completo',
            expiry: '••/••',
            cvc: '•••'
        },

        // if true, will log helpful messages for setting up Card
        debug: false, // optional - default false

        cardFromType : function (e) {
            console.log('formatCardNumber');

        }
    });


    cartao_debito = $('#validateSubmitForm #pagamento-debito').card({
        // a selector or DOM element for the form where users will
        // be entering their information
        /*form: 'validateSubmitForm', */ // *required*
        // a selector or DOM element for the container
        // where you want the card to appear
        container: '.card-wrapper-debito', // *required*

        formSelectors: {
            numberInput: 'input[name="numero_debito"]', // optional — default input[name="number"]
            expiryInput: 'input[name="validade_debito"]', // optional — default input[name="expiry"]
            cvcInput: 'input[name="codigo_debito"]', // optional — default input[name="cvc"]
            nameInput: 'input[name="nome_cartao_debito"]' // optional - defaults input[name="name"]
        },

        width: '280px', // optional — default 280px
        formatting: true, // optional - default true

        // Strings for translation - optional
        messages: {
            validDate: 'Validade', // optional - default 'valid\nthru'
            monthYear: 'mm/aaaa', // optional - default 'month/year'
        },

        // Default placeholders for rendered fields - optional
        placeholders: {
            number: '•••• •••• •••• ••••',
            name: 'Nome Completo',
            expiry: '••/••',
            cvc: '•••'
        },

        // if true, will log helpful messages for setting up Card
        debug: false // optional - default false
    });


    $('input[name="bandeira"]').change(function() {
        $('.parcelamento').hide();
        $('.parcelamento_'+ $(this).val() ).show();
    });

    $('.w-forma-pagamento').click(function() {
        $('#forma_pagamento_tipo_id').val($(this).data('forma'));
    });


    $('#validateSubmitForm').on("submit", function(){

        $('.btn-proximo').attr("disabled","disabled");
        $('.btn-proximo').html('Aguarde...')
        console.log('submit');
        return true;
    });

    $( "#sacado_endereco_cep" ).blur(function() {
        boletoBuscaCep();
    });

    boletoBuscaCep();
    getStatusPedido();

});


function GetCardType(number)
{
    // visa
    var re = new RegExp("^4");
    if (number.match(re) != null)
        return "Visa";

    // Mastercard
    re = new RegExp("^5[1-5]");
    if (number.match(re) != null)
        return "Mastercard";

    // AMEX
    re = new RegExp("^3[47]");
    if (number.match(re) != null)
        return "AMEX";

    // Discover
    re = new RegExp("^(6011|622(12[6-9]|1[3-9][0-9]|[2-8][0-9]{2}|9[0-1][0-9]|92[0-5]|64[4-9])|65)");
    if (number.match(re) != null)
        return "Discover";

    // Diners
    re = new RegExp("^36");
    if (number.match(re) != null)
        return "Diners";

    // Diners - Carte Blanche
    re = new RegExp("^30[0-5]");
    if (number.match(re) != null)
        return "Diners - Carte Blanche";

    // JCB
    re = new RegExp("^35(2[89]|[3-8][0-9])");
    if (number.match(re) != null)
        return "JCB";

    // Visa Electron
    re = new RegExp("^(4026|417500|4508|4844|491(3|7))");
    if (number.match(re) != null)
        return "Visa Electron";

    return "";
}

var ver_redirect = false;
function getStatusPedido(){
    var data = {
        cotacao_id: $('#cotacao_id').val(),
        pedido_id: $('#pedido_id').val()
    }

    var url = $('#url_ver_pedido').val();

//    Pace.track(function(){
    $.ajax({
        type: "POST",
        url: url,
        cache: false,
        data: data,
    })
        .done(function( result ) {
            console.log(data);

            var msg_transacao = '';//result.transacao_message
            if(result.result == true){

                console.log('return', result);
                // não gerou pedido
                if ( !(result.pedido_id > 0) )
                {
                    setTimeout(getStatusPedido, 2000);
                    return;
                }

                //  4 - PAGAMENTO NAO AUTORIZADO
                // 13 - CARRINHO DE COMPRAS
                if ( $.inArray( parseInt(result.pedido_status_id), [4, 13]) != -1 )
                {
                    setTimeout(getStatusPedido, 2000);
                    return;
                }

                var url_proximo =$('#url_pagamento_confirmado').val() + result.pedido_id;
                $('.btn-pagamento-efetuado').attr('href', url_proximo)
                $('.btn-proximo').hide();
                $('.btn-pagamento-efetuado').show();
                toastr.info('PEDIDO FOI PAGO', "Atenção!");

            }else{
                setTimeout(getStatusPedido, 2000);
            }

        });
    //  });



}


function boletoValidarCEP(cep){

    var pattern = /^[0-9]{5}-[0-9]{3}$/;

    if(cep.length > 0){

        if(pattern.test(cep)){

            return true;
        }

    }
    return false;
}


function boletoBuscaCep(){


    var cep = $( "#sacado_endereco_cep" ).val();
    var url = ADMIN_URL + "/helpers/buscar_cep/" + cep;

    if (typeof value === "undefined") {
        return
    }


    if(!boletoValidarCEP(cep)){
        return;
    }




    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function(json){

            if(json.success){

                $("#sacado_endereco_cidade").val(json.data.cidade.toUpperCase());
                $("#sacado_endereco_bairro").val(json.data.bairro.toUpperCase());
                $("#sacado_endereco").val(json.data.tipo_logradouro.toUpperCase() + ' ' + json.data.logradouro.toUpperCase() );
                $("#sacado_endereco_uf").val(json.data.uf);

                $("#sacado_endereco_numero").focus();
            }else {

                alert('Não foi possivel obter informações do cep informado.');
            }
        }
    });


}