
$(function() {

    $("#select2").select2();

    $(".btn-salvar-cotacao").on("click", function() {
        $('#salvar_cotacao').val('1');
        $('#validateSubmitForm').submit();
    });

    $('#modalCoberturas').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var title = button.data('title');

        var price = button.data('price');
        var prices = price.split(',');

        var recipient = button.data('coberturas');
        var coberturas = recipient.split(',');
        var li = '';
        $.each(coberturas, function(key, value) {
            li += '<li> <i class="fa fa-chevron-circle-right success" aria-hidden="true"></i> '+value+' </li>';
        });

        var modal = $(this);
        modal.find('.modal-price').html(prices[0]);
        modal.find('.modal-cents').html(','+prices[1]);
        modal.find('.details-plan').html(li);
    });

    // hidden all input
    var arrayDivs = [];

    $(".form-group").each(function(){
        var divs = $(this)
        arrayDivs.push(divs)

        if(divs.context.innerText != 'CPF'){
           divs.css('display', 'none');
           $('.btn-proximo').attr('disabled', true)
        }
    });
    if($('#cnpj_cpf').length){
        $('#cnpj_cpf').focusout(function(){
            if($(this).val() != ''){
                arrayDivs[1].css('display', 'block')
                $('#nome').focus()
            }
        });
    }
    if($('#nome').length){
        $('#nome').focusout(function(){
            if($(this).val() != ''){
                arrayDivs[2].css('display', 'block')
                $('#email').focus()
            }
        });
    }
    if($('#email').length){
        $('#email').focusout(function(){
            if($(this).val() != ''){
                arrayDivs[3].css('display', 'block')
                $('#telefone').focus()
            }
        });
    }
    if($('#telefone').length){
        $('#telefone').focusout(function(){
            if($(this).val() != ''){
                arrayDivs[4].css('display', 'block')
                $('#data_nascimento').focus()
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