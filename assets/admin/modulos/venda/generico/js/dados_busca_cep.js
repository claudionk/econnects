$(document).ready(function() {
    "use strict";

    $(".buscarCEP").click(function (event ) {


        event.preventDefault();
        var cep =  $('.busca-cep').val();

        if(validarCEP(cep)){

            buscaCep();

        } else {

            alert('CEP inválido.');
        }


    });

    $( ".busca-cep" ).blur(function() {


        var plano_id = $(this).data('plano');
        var cep =  $(this).val();
        if(validarCEP(cep)){

            buscaCep(cep, plano_id);
        }

    });

});

function validarCEP(cep){

    var pattern = /^[0-9]{5}-[0-9]{3}$/;

    if(cep.length > 0){

        if(pattern.test(cep)){

            return true;
        }

    }
    return false;
}


function buscaCep(cep, plano_id){


    var url = ADMIN_URL + "/helpers/buscar_cep/" + cep;


    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function(json){

            if(json.success){

                let aux = '';
                if (plano_id){
                    aux = 'plano_'+ plano_id +'_';
                }

                $("#"+ aux +"endereco_cidade").val(json.data.cidade.toUpperCase());
                $("#"+ aux +"endereco_bairro").val(json.data.bairro.toUpperCase());
                $("#"+ aux +"endereco_logradouro").val(json.data.tipo_logradouro.toUpperCase() + ' ' + json.data.logradouro.toUpperCase() );
                $("#"+ aux +"endereco_estado").val(json.data.uf);
                $("#"+ aux +"endereco_numero").focus();
            }else {

                alert('Não foi possivel obter informações do cep informado.');
            }
        }
    });


}