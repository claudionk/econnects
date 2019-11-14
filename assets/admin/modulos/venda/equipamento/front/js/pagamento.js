var cartao;
var cartao_debito;

$(document).ready(function() {

    $('#validade').datepicker({autoclose: true, todayHighlight: true, format: "mm/yyyy", startDate: '0d', language: 'pt-BR', forceParse : false,  minViewMode: 1});
    $('#validade_debito').datepicker({autoclose: true, todayHighlight: true, format: "mm/yyyy", startDate: '0d', language: 'pt-BR', forceParse : false,  minViewMode: 1});

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
        cotacao_id: $('#cotacao_id').val()
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
                if((result.pedido_id > 0) && (result.pedido_status_id != 4) ){
                    var url_proximo =$('#url_pagamento_confirmado').val() + result.pedido_id;
                    $('.btn-pagamento-efetuado').attr('href', url_proximo)
                    $('.btn-proximo').hide();
                    $('.btn-pagamento-efetuado').show();
                    toastr.info('PEDIDO FOI PAGO', "Atenção!");
                }else{
                    setTimeout(getStatusPedido, 2000);
                }
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

function mostraInput(id){
    //$(".form-group").find('input').focus().next("div").css( 'display', 'block' );
    var mudar = document.getElementById("float_btn"); 
    
    document.getElementById(id).style.display = "block";
    if (id == 'div_nome'){
        mudar.setAttribute('onclick', 'mostraInput("div_cep")');
        $("#sacado_nome").focus();
    }else if (id == 'div_cep'){
        mudar.setAttribute('onclick', 'boletoBuscaCep()');
        $("#sacado_endereco_cep").focus();  
    }   
}

function boletoBuscaCep(){
    var cep = $( "#sacado_endereco_cep" ).val();
    var url = ADMIN_URL + "/helpers/buscar_cep/" + cep;

    if (typeof cep === "undefined") {
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

                document.getElementById("div_endereco").style.display = "block";
                document.getElementById("div_num").style.display = "block";
                document.getElementById("div_compl").style.display = "block";
                document.getElementById("div_bairro").style.display = "block";
                document.getElementById("div_cidade").style.display = "block";
                document.getElementById("div_uf").style.display = "block";
                document.getElementById("div_float").style.display = "none";
                $('#btn-proximo').attr('disabled', false); 
                $("#sacado_endereco_num").focus();
            }else {

                alert('Não foi possivel obter informações do cep informado.');
            }
        }
    });
}

function selectFormaPagamento(){

    var f_pagamento = $('#formaPagamento').val();

    $('.forma-pagamento').fadeOut('slow');
    $('#btnSubmit').fadeOut('show');

    // cartão de crédito
    if(f_pagamento == 1){
        $('#pagamento-credito').fadeIn('show');
        $('#btnSubmit').fadeIn('show');
        $('#btn-proximo').attr('disabled', false); 
    }

    // cartão de débito
    if(f_pagamento == 8){
        $('#pagamento-debito').fadeIn('show');
        $('#btnSubmit').fadeIn('show');
        $('#btn-proximo').attr('disabled', false); 
    }

    // boleto pagmax
    if(f_pagamento == 9){
        $('#pagamento-boleto').fadeIn('show');
        $('#btnSubmit').fadeIn('show');
        $('#btn-proximo').attr('disabled', true);

    }

    console.log($('#formaPagamento').val());
}
$(function() {
    $('[data-toggle="tooltip"]').tooltip()

    $(".validade_cartao").inputmask("m/y",{ "placeholder": "__/____" });

    $('.numeros_cartao').inputmask("9999 9999 9999 9999",{ "placeholder": "____ ____ ____ ____" });
});