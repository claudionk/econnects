$(document).ready(function() {

    $(".comboEstado").change(function(){



        var url = ADMIN_URL + "/localidade_cidades/buscaCidadesPorEstado";

        var estado_id = $("#localidade_estado_id").val();

        $.ajax({
            url: url,
            type: "POST",
            dataType: "json",
            data: { idEstado: estado_id },
            success: function(json){
                var options = ' <option value=""></option>';

                $.each(json.rows, function(key, value){

                    options += '<option value="' + value.localidade_cidade_id + '">' + value.nome + '</option>';
                });

                $("#localidade_cidade_id").html(options);
            }
        });

    });


    $( ".buscarCEP" ).click(function() {

        event.preventDefault();


        var cep =  $('#cep').val();

        if(validarCEP(cep)){

            buscaCep();
        }else {

            alert('CEP Inválido');
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


                $("#bairro").val(json.data.bairro.toUpperCase());
                $("#endereco").val(json.data.tipo_logradouro.toUpperCase() + ' ' + json.data.logradouro.toUpperCase() );


                if(json.data.uf != ''){
                    $("#localidade_estado_id").val(json.data.estado_id);


                }



                if(json.data.cidades.length){

                    var options = ' <option value=""></option>';

                    $.each(json.data.cidades, function(key, value){

                        options += '<option value="' + value.localidade_cidade_id + '">' + value.nome + '</option>';
                    });

                    $("#localidade_cidade_id").html(options);
                }

                if(json.data.cidade != ''){
                    $("#localidade_cidade_id").val(json.data.cidade_id);
                }



            }else {

                alert('Não foi possivel obter informações do cep informado.', 'Aviso');
            }
        }
    });

}

function buscaCidades(){


}