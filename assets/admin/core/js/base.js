$(function(){

    $('.deleteRowButton').on('click', function(){

        return confirm("Deseja realmente excluir esse registro?");
    });

    $(".trocarParceiro").click(function(){

        $.ajax({
            type: "POST",
            url: base_url + "admin/colaboradores/trocarParceiro/" + $(this).attr('id'),
            dataType: 'json',
            success: function(resposta){
                if(resposta.status)
                {
                    window.location.reload();
                }
            },


        });
    })

    $.extend($.inputmask.defaults, {
        'autounmask': true
    });

    $('.datepicker, .inputmask-date').datepicker({
        format: 'dd/mm/yyyy',
        language: 'pt-BR'
    });

    function setFieldInvalid(){
        var validator = $('#validateSubmitForm').validate();
        validator.showErrors({
          'email' : 'E-mail inválido'
        });
    }

    $('.validateEmail').on('blur', function (e) {
        var el = this;
        var email = $.trim($(el).val()), parse_email = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
        if (email != '' && parse_email.test(email) ) {
            $.ajax({
                url: base_url + "admin/api/email/"+ encodeURIComponent(email),
            }).success(function (data) {
                if (!data){
                    setFieldInvalid();
                }

            }).error(function (response) {
                console.log(response);
            });
        } else {
            setFieldInvalid();
        }

    });

    $(".inputmask-date").inputmask("d/m/y", {autoUnmask: true});
    $(".inputmask-cpf").inputmask({"mask": "999.999.999-99"});
    $(".inputmask-cnpj").inputmask({"mask": "99.999.999/9999-99"});
 	$(".inputmask-celular").inputmask("mask", {"mask": "(99)9999-99999"});
    $(".inputmask-telefone").inputmask("mask", {"mask": "(99)9999-9999"});
    $(".inputmask-moeda").inputmask('999,99', { numericInput: true, rightAlignNumerics: false, greedy: true});
    $(".inputmask-moeda2").inputmask('999.999.999,99', { numericInput: true, rightAlignNumerics: false, greedy: true});
    $(".inputmask-valor").inputmask('999.999.999,99', { numericInput: true, rightAlignNumerics: false, greedy: true});
    $(".inputmask-numero").inputmask('', {numericInput: true, rightAlignNumerics: false});
    $(".inputmask-cep").inputmask("mask", {"mask": "99999-999"});
    $(".time-mask").inputmask('h:s', {placeholder: 'hh:mm'});


    $('.select2-list').select2({
        allowClear: true
    });
    /**
     * Faturas @todo trocar de lugar
     */
    $('.btn-parcelas').on('click', function(){
        var fatura_id = $(this).data('fatura');
        $('.grid-grouped-'+ fatura_id).toggle( "slow");


    });

    $('#validateSubmitForm').find('input[type=text],textarea,select').first().focus();


    $("#checkAll").click(function(){
        $('input:checkbox.checkbox_row').not(this).prop('checked', this.checked);
    });


});


if (navigator.userAgent.toLowerCase().indexOf("chrome") >= 0) {
    $(window).load(function(){
        $('input:-webkit-autofill').each(function(){
            var text = $(this).val();
            var name = $(this).attr('name');
            $(this).after(this.outerHTML).remove();
            $('input[name=' + name + ']').val(text);
        });
    });}

/**
 *
 * @param n numero a converter
 * @param c  numero de casas decimais
 * @param d separador decimal
 * @param t separador milhar
 * @returns {string}
 */

function numeroParaMoeda(n, c, d, t)
{
    c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

/**
 * Mascara para Date
 * @param mask
 * @returns {Date}
 */
function maskToDate(mask)
{
    var mask = mask.split("/");
    return new Date(mask[2], mask[1] - 1, mask[0]);
}

/**
 * Verifica data válida
 * @param data
 * @returns {boolean}
 */
function dataValida(data)
{
    var RegExPattern = /^((((0?[1-9]|[12]\d|3[01])[\.\-\/](0?[13578]|1[02])      [\.\-\/]((1[6-9]|[2-9]\d)?\d{2}))|((0?[1-9]|[12]\d|30)[\.\-\/](0?[13456789]|1[012])[\.\-\/]((1[6-9]|[2-9]\d)?\d{2}))|((0?[1-9]|1\d|2[0-8])[\.\-\/]0?2[\.\-\/]((1[6-9]|[2-9]\d)?\d{2}))|(29[\.\-\/]0?2[\.\-\/]((1[6-9]|[2-9]\d)?(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)|00)))|(((0[1-9]|[12]\d|3[01])(0[13578]|1[02])((1[6-9]|[2-9]\d)?\d{2}))|((0[1-9]|[12]\d|30)(0[13456789]|1[012])((1[6-9]|[2-9]\d)?\d{2}))|((0[1-9]|1\d|2[0-8])02((1[6-9]|[2-9]\d)?\d{2}))|(2902((1[6-9]|[2-9]\d)?(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)|00))))$/;

    if (!((data.match(RegExPattern)) && (data!=''))) {
        return true
    }

    return false;
}

function parseNumero(valor)
{
    var v = parseFloat(valor);

    if(v.toString() == "NaN")
    {
        return 0;
    }
    return v;
}


function busca_cliente(){

    var data = {
        cpf: $('#cnpj_cpf').val(),
        produto_parceiro_id: $('#produto_parceiro_id').val(),
    }

    var url = base_url + 'admin/clientes/get_cliente';

    $.ajax({
        type: "POST",
        url: url,
        cache: false,
        data: data,
    }).done(function( result ) {
        console.log('result', result);
        if((result.sucess == true) && result.qnt > 0){
            $('#nome').val(result.nome);
            $('#data_nascimento').val(result.data_nascimento);
            $('#email').val(result.email);
            console.log('telefone', $('#telefone').val());
            if(!$('#telefone').val()){
                $('#telefone').val(result.telefone);
            }
            //

            $('#estado_civil').val(result.estado_civil);
            $('#sexo').val(result.sexo);
            $('#rg_orgao_expedidor').val(result.rg_orgao_expedidor);
            $('#rg_uf').val(result.rg_uf);
            $('#rg_data_expedicao').val(result.rg_data_expedicao);

            $('#rg').val(result.rg);

            $('#seguro_viagem_motivo_id').focus();
            if(result.cliente_id > 0){
                $('.ls-modal').removeClass('disabled');
                $('.ls-modal').on('click', function (e) {
                    e.preventDefault();
                    $('#detalhe-cliente').modal('show').find('.modal-body').load($(this).attr('href') + '/' + result.cliente_id);
                });
            }else{
                $('.ls-modal').addClass('disabled');
            }
			return;
        }
    });

    // Busca no SGS
    var url = "https://sgs-h.jelastic.saveincloud.net/v1/api/segurado/econnects?seg_doc=" + $('#cnpj_cpf').val();
    console.log( url );
    $.ajax({
        type: "GET",
        url: url,
    }).done(function( result ) {
      console.log( result );
      $('#nome').val(result.DadosSegurado.nome);
      $('#data_nascimento').val(result.DadosSegurado.data_nascimento);
      $('#email').val(result.DadosSegurado.email);
      if(!$('#telefone').val()){
        $('#telefone').val(result.DadosSegurado.telefone);
      }
      $('#sexo').val(result.DadosSegurado.sexo);
      $('#seguro_viagem_motivo_id').focus();
      $('input[name=equipamento_id]').val(result.DadosSegurado.modelo);
      $('#nota_fiscal_data').val(result.DadosSegurado.nota_data);
      $('#nota_fiscal_valor').val(result.DadosSegurado.nota_valor);
      if(result.cliente_id > 0){
        $('.ls-modal').removeClass('disabled');
        $('.ls-modal').on('click', function (e) {
          e.preventDefault();
          $('#detalhe-cliente').modal('show').find('.modal-body').load($(this).attr('href') + '/' + result.cliente_id);
        });
      }else{
        $('.ls-modal').addClass('disabled');
      }
      return;
    });

}

function buscaDadosEAN(){
    var ean = $('#ean').val();

    if (typeof ean == 'undefined' || ean == null || ean == '')
        return false;

    $.ajax({
        url: base_url + "admin/api/equipamento/"+ ean,
    }).success(function (data) {
        console.log(data);

        if (!data){
            return false;
        }

        if (typeof data.status != 'undefined' && data.status == false) {
            return false;
        }

        populaSelectCategoria(data.equipamento_categoria_id);
        populaSelectMarca(data.equipamento_marca_id);
        populaSelectMarca(data.equipamento_id);

    }).error(function (response) {
        console.log(response);
    });


}
