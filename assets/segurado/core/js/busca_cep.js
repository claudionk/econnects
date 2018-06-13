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



        var cep =  $(this).val();

        if(validarCEP(cep)){

            buscaCep();
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


function buscaCep(){

    var cep = $('#cep').val();


    var url = ADMIN_URL + "/helpers/buscar_cep/" + cep;

    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function(json){

            if(json.success){

                $("#cidade").val(json.data.cidade.toUpperCase());
                $("#bairro").val(json.data.bairro.toUpperCase());
                $("#endereco").val(json.data.tipo_logradouro.toUpperCase() + ' ' + json.data.logradouro.toUpperCase() );


                if(json.data.uf != ''){
                    $("#uf").val(json.data.uf);
                    $('#uf').select2('val', json.data.uf);
                }


            }else {

                alert('Não foi possivel obter informações do cep informado.');
            }
        }
    });


}