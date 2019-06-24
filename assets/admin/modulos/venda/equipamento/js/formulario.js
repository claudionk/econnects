$(document).ready(function(){

    $('#cnpj_cpf').on('blur',function() {
        busca_cliente();
    });

    // hidden all input
    var arrayDivs = [];

    $(".form-group").each(function(key, values){
        var divs = $(this)
        arrayDivs.push(divs)

        console.log(values)

        divs.css('display', 'none');
        
        $('.btn-proximo').attr('disabled', true)

        if(key == 0){
            divs.css('display', 'block');
        }
    });
    if($('#cnpj_cpf').length){
        $('#cnpj_cpf').focusout(function(){
            if($(this).val() != ''){
                arrayDivs[1].css('display', 'block')
                $('#nome').focus()
            }else{
                $(this).focus()
            }
        });
    }
    if($('#nome').length){
        $('#nome').focusout(function(){
            if($(this).val() != ''){
                arrayDivs[2].css('display', 'block')
                $('#email').focus()
            }else{
                $(this).focus()
            }
        });
    }
    if($('#email').length){
        $('#email').focusout(function(){
            if($(this).val() != ''){
                arrayDivs[3].css('display', 'block')
                $('#telefone').focus()
            }else{
                $(this).focus()
            }
        });
    }
    if($('#telefone').length){
        $('#telefone').focusout(function(){
            if($(this).val() != ''){
                arrayDivs[4].css('display', 'block')
                $('#data_nascimento').focus()
            }else{
                $(this).focus()
            }
        });
    }
    if($('#data_nascimento').length){
        $('#data_nascimento').focusout(function(){
            if($(this).val() != ''){
                arrayDivs[5].css('display', 'block')
                $('#rg_data_expedicao').focus()
            }
        });
    }
    if($('#rg_data_expedicao').length){
        $('#rg_data_expedicao').focusout(function(){
            if($(this).val() != ''){
                arrayDivs[6].css('display', 'block')
                arrayDivs[7].css('display', 'block')
                $('#ean').focus()
            }
        });
    }
    if($('#ean').length){
        $('#ean').focusout(function(){
            arrayDivs[8].css('display', 'block')
            arrayDivs[9].css('display', 'block')
            arrayDivs[10].css('display', 'block')
            arrayDivs[11].css('display', 'block')
            arrayDivs[12].css('display', 'block')
            $('.btn-proximo').attr('disabled', false)
        });
    }
});
