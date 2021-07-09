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

    });

    $('#ean').on('blur',function() {
        buscaDadosEAN();
    });

    $('#imei').on('blur',function() {
        // buscaDadosIMEI();
    });

    $.extend($.inputmask.defaults, {
        'autounmask': true
    });

    $('.datepicker, .inputmask-date').datepicker({
        format: 'dd/mm/yyyy',
        language: 'pt-BR'
    });

    // Datas que não podem ser futuras
    $('.datepicker.notfuture').datepicker('setEndDate', new Date());

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

    $(".mask-custom").each(function(){
        // debugger;
        var v = $(this).attr('class').split(' ');
        for (var i = 0; i < v.length; i++) {
            if ( c = v[i].indexOf('mask-custom=') >= 0 )
            {
                c = v[i].replace('mask-custom=', '');
                $(this).attr('length', c.length).inputmask({"mask": c});
                break;
            } 
        }
    })

    $(".inputmask-date").inputmask({"mask": "99/99/9999"});
    // $(".inputmask-cpf").inputmask({
    //     mask: ['999.999.999-99'],
    //     keepStatic: true
    // });
    $(".inputmask-cpf").inputmask({"mask": '999.999.999-99'});
    $(".inputmask-cnpj").inputmask({"mask": "99.999.999/9999-99"});
 	$(".inputmask-celular").inputmask("mask", {"mask": "(99) 99999-9999"});
    $(".inputmask-telefone").inputmask("mask", {"mask": "(99) 9999-9999"});
    $(".inputmask-moeda").inputmask('999,99', { numericInput: true, rightAlignNumerics: false, greedy: true});
    $(".inputmask-moeda2").inputmask('999.999.999,99', { numericInput: true, rightAlignNumerics: false, greedy: true});
    $(".inputmask-valor")
        .maskMoney({symbol:'R$ ', thousands:'.', decimal:',', symbolStay: true});
    //     .inputmask( 'currency',{
    //         "autoUnmask": true,
    //         radixPoint:",",
    //         groupSeparator: ".",
    //         allowMinus: false,
    //         prefix: 'R$ ',            
    //         digits: 2,
    //         digitsOptional: false,
    //         rightAlign: true,
    //         unmaskAsNumber: true
    // });
    $(".inputmask-numero").inputmask('', {numericInput: true, rightAlignNumerics: false});
    $(".inputmask-cep").inputmask("mask", {"mask": "99999-999"});
    $(".time-mask").inputmask('h:s', {placeholder: 'hh:mm'});

    $('.select2-list').select2({
        allowClear: true
    });

    var lista_id = busca_lista_id();

    /**
     * Faturas @todo trocar de lugar
     */
    $('.btn-parcelas').on('click', function(){
        var fatura_id = $(this).data('fatura');
        $('.grid-grouped-'+ fatura_id).toggle( "slow");
    });

    $('#validateSubmitForm').find('input[type=text],input[type=tel],textarea,select').first().focus();


    $("#checkAll").click(function(){
        $('input:checkbox.checkbox_row').not(this).prop('checked', this.checked);
    });

    //busca produtos conforme o codigo da tabela do cliente selecionado
    $(".js-categorias-ajax").select2({
        ajax: {
            url: base_url + "admin/equipamento/service_categorias",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true

        },
        escapeMarkup: function (markup) { return markup ; }, // let our custom formatter work
        minimumInputLength: 1,
        templateResult: formatRepoCategoriasEquipamento, // omitted for brevity, see the source of this page*/
        templateSelection: formatRepoSelectionEquipamento, // omitted for brevity, see the source of this page*/
        language: "pt-BR"
    });

    //verifica se é uma edição ou POST
    if($(".js-categorias-ajax").data('selected') != ''){
        populaSelectCategoria($(".js-categorias-ajax").data('selected'));
    }

    //busca produtos conforme o codigo da tabela do cliente selecionado
    $(".js-equipamento_marca_id-ajax").select2({
        ajax: {
            url: base_url + "admin/equipamento/service_marcas",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page,
                    categoria_id: ($(".js-categorias-ajax").val() != '') ? $(".js-categorias-ajax").val() : 0,
                    lista_id: lista_id
                };
            },
            processResults: function (data, params)
            {
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) { return markup ; }, // let our custom formatter work
        minimumInputLength: 1,
        templateResult: formatRepoCategoriasEquipamento, // omitted for brevity, see the source of this page*/
        templateSelection: formatRepoSelectionEquipamento, // omitted for brevity, see the source of this page*/
        language: "pt-BR"
    });

    //verifica se é uma edição ou POST
    if($(".js-equipamento_marca_id-ajax").data('selected') != ''){
        var $jsEquipamentoMarcaIdAjax = $(".js-equipamento_marca_id-ajax");
        var multiple = $jsEquipamentoMarcaIdAjax.attr("multiple");
        if(multiple){
            populaSelectMarca($(".js-equipamento_marca_id-ajax").data('selected'), multiple);
        }
        
    }
    //busca produtos conforme o codigo da tabela do cliente selecionado
    $(".js-equipamento_sub_categoria_id-ajax").select2({
        ajax: {
            url: base_url + "admin/equipamento/service_categorias/0/2",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page,
                    categoria_pai_id: ($(".js-categorias-ajax").val() != '') ? $(".js-categorias-ajax").val() : 0,
                    marca_id: ($(".js-equipamento_marca_id-ajax").val() != '' && $(".js-equipamento_marca_id-ajax").attr("multiple") != "multiple") ? $(".js-equipamento_marca_id-ajax").val() : 0,
                    lista_id: lista_id
                };
            },
            processResults: function (data, params)
            {
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) { return markup ; }, // let our custom formatter work
        minimumInputLength: 1,
        templateResult: formatRepoCategoriasEquipamento, // omitted for brevity, see the source of this page*/
        templateSelection: formatRepoSelectionEquipamento, // omitted for brevity, see the source of this page*/
        language: "pt-BR"
    });

    //verifica se é uma edição ou POST
    if($(".js-equipamento_sub_categoria_id-ajax").data('selected') != ''){
        populaSelectSubCategoria($(".js-equipamento_sub_categoria_id-ajax").data('selected'));
    }

    //busca produtos conforme o codigo da tabela do cliente selecionado
    $(".js-equipamento_id-ajax").select2({
        ajax: {
            url: base_url + "admin/equipamento/service",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page,
                    categoria_id: ($(".js-categorias-ajax").val() != '') ? $(".js-categorias-ajax").val() : 0,
                    sub_categoria_id: ($(".js-equipamento_sub_categoria_id-ajax").val() != '') ? $(".js-equipamento_sub_categoria_id-ajax").val() : 0,
                    marca_id: ($(".js-equipamento_marca_id-ajax").val() != '') ? $(".js-equipamento_marca_id-ajax").val() : 0,
                    lista_id: lista_id
                };
            },
            processResults: function (data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) { return markup ; }, // let our custom formatter work
        minimumInputLength: 1,
        templateResult: formatRepoCategoriasEquipamento, // omitted for brevity, see the source of this page*/
        templateSelection: formatRepoSelectionEquip, // omitted for brevity, see the source of this page*/
        language: "pt-BR"
    });

    //verifica se é uma edição ou POST
    if($(".js-equipamento_id-ajax").data('selected') != ''){
        populaSelectModelo($(".js-equipamento_id-ajax").data('selected'));
    }

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


function busca_cotacao_salva(){

    var data = {
        cpf: $('.cnpj_cpf').val(),
        produto_parceiro_id: $('#produto_parceiro_id').val(),
    }

    var url = base_url + 'admin/venda/cotacao_salva';

    $.ajax({
        type: "POST",
        url: url,
        cache: false,
        data: data,
    }).done(function( result ) {
        console.log('result', result);
        if(result.sucess == true) {


            var url_redirect = base_url + 'admin/venda_'+ result.produto_slug +  '/' +result.produto_slug + '/' + $('#produto_parceiro_id').val() + '/2/' + result.cotacao_id;
            window.location.href = url_redirect;
            return;
        }
    });
}

function busca_lista_id()
{
    return (typeof $("#lista_id").val() !== 'undefined' && $("#lista_id").val() != '') ? $("#lista_id").val() : 1;
}

function busca_cliente(){

    var data = {
        cpf: $('.busca_cliente').val(),
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
            $('.nome.enriquecer').val(result.nome);
            $('.data_nascimento.enriquecer').val(result.data_nascimento);
            $('.data_nascimento.enriquecer').datepicker('setDate', result.data_nascimento);

            // $('#email').val(result.email);
            console.log('telefone', $('.telefone.enriquecer').val());
            if(!$('.telefone.enriquecer').val()){
                $('.telefone.enriquecer').val(result.telefone);
            }

            $('.estado_civil.enriquecer').val(result.estado_civil);
            $('.sexo.enriquecer').val(result.sexo);
            $('.rg_orgao_expedidor.enriquecer').val(result.rg_orgao_expedidor);
            $('.rg_uf.enriquecer').val(result.rg_uf);
            $('.rg_data_expedicao.enriquecer').val(result.rg_data_expedicao);

            $('.rg.enriquecer').val(result.rg);

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
    var url = SISGlobal.config.URL_SGS+"v1/api/segurado/econnects?seg_doc=" + $('#cnpj_cpf').val();
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
        populaSelectSubCategoria(data.equipamento_sub_categoria_id);
        populaSelectMarca(data.equipamento_marca_id);
        populaSelectModelo(data.equipamento_id);

    }).error(function (response) {
        console.log(response);
    });
}
function populaSelectCategoria(id){
    if (!id) return false;
    if ($(".js-categorias-ajax").val() == id) return false;

    $.ajax({
        url: base_url + "admin/equipamento/service_categorias/" + id,
        type: "GET",
        async: false,
        dataType: "json",
        success: function(data){
            $(".js-categorias-ajax").select2("trigger", "select", {
                data: data.items
            });
        },
        error: function(error){
            console.log("Error:", error);
        }
    });
}
function populaSelectSubCategoria(id){
    if (!id) return false;
    if ($(".js-equipamento_sub_categoria_id-ajax").val() == id) return false;

    lista_id = busca_lista_id();
    var $data = {}, url='/'+encodeURI(id);
    if (String(id).indexOf(",") >= 0 || String(id).indexOf("'") >= 0) {
        id = id.replace(/'/g, '');
        var x = id.split(",");

        if (x.length > 1) {
            $data = Object.assign({}, x), url = '/0';
        } else {
            url='/'+encodeURI(x[0]);
        }
    }
    $.ajax({
        url: base_url + "admin/equipamento/service_categorias"+ url +"/2?lista_id="+ lista_id,
        type: "POST",
        async: false,
        data: $data,
        dataType: "json",
        success: function(data){
            var vet = data.items;
            if (typeof data.items.length == 'undefined') {
                var vet = {0: data.items};
            }

            for (var key in vet) {
                $(".js-equipamento_sub_categoria_id-ajax").select2("trigger", "select", {
                    data: vet[key]
                });
            }
        },
        error: function(error){
            console.log("Error:", error);
        }
    });
}
function populaSelectMarca(id, multiple){
    if (!id) return false;
    if ($(".js-equipamento_marca_id-ajax").val() == id) return false;

    var ajaxData = {
        async: false,
        dataType: "json",
        success: function(data){
            $(".js-equipamento_marca_id-ajax").each(function(index){
                if($(this).attr("multiple")){
                    for (var i in data.items) {
                        $(this).select2("trigger", "select", {
                            data: data.items[i]
                        });
                    }
                } else {
                    $(this).select2("trigger", "select", {
                        data: data.items
                    });
                }
            });
            
        },
        error: function(error){
            console.log("Error:", error);
        }
    };

    if(multiple){

        ajaxData.url = base_url + "admin/equipamento/service_marcas/";
        ajaxData.type = "POST";
        
        ajaxData.data = {};
        if (String(id).indexOf(",") >= 0 || String(id).indexOf("'") >= 0) {
            var chave = 0;
            var aMarcaId = id.replace(/'/g, '').split(",");
            for(chave in aMarcaId){
                ajaxData.data[chave] = aMarcaId[chave];
            }
        }

    } else {
        ajaxData.url = base_url + "admin/equipamento/service_marcas/" + id;
        ajaxData.type = "GET";
    }

    $.ajax(ajaxData);
}
function populaSelectModelo(id){
    if (!id) return false;
    if ($(".js-equipamento_id-ajax").val() == id) return false;

    var $data = {}, url='/'+encodeURI(id);
    if (String(id).indexOf(",") >= 0) {
        id = id.replace(/'/g, '');
        var x = id.split(",");

        if (x.length > 1) {
            $data = Object.assign({}, x), url = '';
        } else {
            url='/'+encodeURI(x[0]);
        }
    }

    $.ajax({
        url: base_url + "admin/equipamento/service"+ url,
        type: "POST",
        data: $data,
        async: false,
        dataType: "json",
        success: function(data){

            var vet = data.items;
            if (typeof data.items.length == 'undefined') {
                var vet = {0: data.items};
            }

            for (var key in vet) {
                $(".js-equipamento_id-ajax").select2("trigger", "select", {
                    data: vet[key]
                });
            }
        },
        error: function(error){
            console.log("Error:", error);
        }
    });
}
function formatRepoCategoriasEquipamento (repo) {
    if (repo.loading) return repo.text;

    var markup = "<div class='select2-result-repository clearfix'>" +
        "<div class='select2-result-repository__meta'>" +
        "<div class='select2-result-repository__title'>" + repo.nome + "<br/></div>";

    return markup;
}
function formatRepoSelectionEquipamento (repo) {
    return repo.nome || repo.ean;
}
function formatRepoSelectionEquip (repo) {
    var nome = repo.nome || repo.ean;

    if (typeof nome != 'undefined') {
        $('#ean').val(repo.ean);
        $('#equipamento_nome').val(nome);

        populaSelectCategoria(repo.equipamento_categoria_id);
        populaSelectSubCategoria(repo.equipamento_sub_categoria_id);
        populaSelectMarca(repo.equipamento_marca_id);

    }
    return nome;
}
function formatRepoEquipamento (repo) {
    if (repo.loading) return repo.text;

    var markup = "<div class='select2-result-repository clearfix'>" +
        // "<div class='select2-result-repository__avatar'></div>" +
        "<div class='select2-result-repository__meta'>" +
        "<div class='select2-result-repository__title'>Equipamento: " + repo.nome + "<br/><span class='text-bold'>EAN:"+ repo.ean +"</span></div>";

    if (repo.descricao) {
        markup += "<div class='select2-result-repository__description'>" + repo.descricao + "</div>";
    }

    markup += "<div class='select2-result-repository__statistics'>" +
        "<div class='select2-result-repository__forks'><i class='fa fa-map-marker'></i> <span class='text-bold'>MARCA: </span>" + repo.equipamento_marca_nome + "</div>" +
        "<div class='select2-result-repository__forks'><i class='fa fa-folder-open'></i> <span class='text-bold'>LINHA / CATEGORIA: </span>" + repo.equipamento_categoria_nome + " - " + repo.equipamento_categoria_codigo + "</div>" +
        "<div class='select2-result-repository__forks'><i class='fa fa-folder-open'></i> <span class='text-tags'>TAGS: </span>" + repo.tags + "</div>" +
        "</div>" +
//        "<div class='select2-result-repository__statistics'>" +
//        "<div class='select2-result-repository__forks'><i class='fa fa-anchor'></i><span class='text-bold'> Peso: </span>" + repo.peso + "</div>" +
//        "<div class='select2-result-repository__forks'><i class='fa fa-home'></i><span class='text-bold'> IPI:</span> " + repo.ipi + "</div>" +
//        "<div class='select2-result-repository__forks'><i class='fa fa-flash'></i><span class='text-bold'> Tensão:</span> " + repo.voltagem + "</div>" +
//        "<div class='select2-result-repository__forks'><i class='fa fa-dot-circle-o'></i><span class='text-bold'> MONO/TRI:</span> " + repo.tipo_voltagem + "</div>" +
//        "<div class='select2-result-repository__forks'><i class='fa fa-sellsy'></i><span class='text-bold'> Aplicação:</span> " + repo.aplicacao + "</div>" +
//        "</div>" +
        "</div></div>";

    return markup;
}
