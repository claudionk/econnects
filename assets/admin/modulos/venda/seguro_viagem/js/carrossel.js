$(function(){


    if($("#slider-preco-two-container").hasClass( "preco_slider" )){
        $("#vendas_planos_preco .preco_slider ul li").width($("#vendas_planos_preco").width() / 2);
        $("#vendas_planos .plano_slider ul li").width($("#slider-one-container").width());
    }else{
        $("#vendas_planos_preco .preco_slider ul li").width($("#vendas_planos_preco").width() - 1);
        $("#vendas_planos .plano_slider ul li").width($("#slider-one-container").width() -1);
    }


    var planoSlideTwoClone = $("#slider-two-container").html();
    var planoPrecoSlideTwoClone = $("#slider-preco-two-container").html();

    var planoSliderOneLock = false;

    $( '#plano_slider_one' ).cycle({
        slides: ' > li',
        startinSlide: 0,
        log: false,
        timeout: 0,
        fx: 'carousel'

    });
    $( '#plano_slider_preco_one' ).cycle({
        slides: ' > li',
        startinSlide: 0,
        log: false,
        timeout: 0,
        fx: 'carousel'

    });



    var init_slide = 1;

    if( $( "#plano_slider_two li" ).length == 2){
        init_slide = 1;
    }

    $( '#plano_slider_two' ).cycle({
        slides: ' > li',
        startingSlide: init_slide,
        timeout: 0,
        log: false,
        fx: 'carousel'
    });
    $( '#plano_slider_preco_two' ).cycle({
        slides: ' > li',
        startingSlide: init_slide,
        timeout: 0,
        log: false,
        fx: 'carousel'
    });

    $('.ck-cobertura-adicional').click(function () {


        if ($(this).is(':checked')) {
            $('.sp-cobertura-adicional_' + $(this).val().replace(";", "_")).addClass('text-bold');
            $('.sp-cobertura-adicional_' + $(this).val().replace(";", "_")).addClass('text-primary');
        }else{
            $('.sp-cobertura-adicional_' + $(this).val().replace(";", "_")).removeClass('text-bold');
            $('.sp-cobertura-adicional_' + $(this).val().replace(";", "_")).removeClass('text-primary');
        }
        calculo_preco();
    });

    $('.plano_header').on('click', function(e){

        if(planoSliderOneLock){


            $('#plano_slider_two').cycle('destroy');
            $('#plano_slider_two').remove();
            $("#slider-two-container").empty();

            $('#plano_slider_preco_two').cycle('destroy');
            $('#plano_slider_preco_two').remove();
            $("#slider-preco-two-container").empty();


            $( "#slider-two-container" ).html(planoSlideTwoClone);
            $( "#slider-preco-two-container" ).html(planoPrecoSlideTwoClone);

            var init_slide = 1;

            if( $( "#plano_slider_two li" ).length == 2){
                init_slide = 1;
            }

            $( '#plano_slider_two' ).cycle({
                slides: ' > li',
                startingSlide: init_slide,
                timeout: 0,
                fx: 'carousel'
            });
            $( '#plano_slider_preco_two' ).cycle({
                slides: ' > li',
                startingSlide: init_slide,
                timeout: 0,
                fx: 'carousel'
            });

            /**
             * reaplica as coberturas na nova instancia do slide
             */
            reloadCoberturaMark();


            /**
             * reaplica o evento de clique em adicionar coberturas
             */
            applyClickCobertura();


            /**
             * reaplica o uniform nos botoes para contratar
             */
            applyUniform();

            /**
             * reaplica o botao contratar na nova instancia do slide
             */

            applyChangeContratar();
            reloadChangeContratar();


            /**
             * Ao destravar reincia slide para ordem correta
             * */
            $('#plano_slider_one').cycle('goto', 0);
            $('#plano_slider_preco_one').cycle('goto', 0);

            $('#plano_slider_preco_one').parent().removeClass('preco_one_plano_lock');
            console.log('unlock');

            planoSliderOneLock = false;

            $(this).removeClass('plano_lock');
            $(this).removeClass('text-primary');
            $(this).parent().parent().parent().removeClass('table_plano_lock');

            $(".cadeado.one").addClass("fa-unlock-alt");
            $(".cadeado.one").removeClass("fa-lock");
        }
        else
        {
            $('#plano_slider_two').cycle('remove',  $("#plano_slider_one").data("cycle.opts").currSlide);
            $('#plano_slider_preco_two').cycle('remove',  $("#plano_slider_preco_one").data("cycle.opts").currSlide);

            $(".cadeado.one").removeClass("fa-unlock-alt");
            $(".cadeado.one").addClass("fa-lock");

            $('#plano_slider_preco_one').parent().addClass('preco_one_plano_lock');
            console.log('lock');
            planoSliderOneLock = true;

            $(this).addClass('plano_lock');
            $(this).addClass('text-primary');
            $(this).parent().parent().parent().addClass('table_plano_lock');

        }

        calculo_preco();

        return false;
    });


    $('.plano_prev').on('click', function(e)
    {
        e.preventDefault();

        if(!planoSliderOneLock)
        {
            $('#plano_slider_one').cycle('prev');
            $('#plano_slider_two').cycle('prev');
            $('#plano_slider_preco_one').cycle('prev');
            $('#plano_slider_preco_two').cycle('prev');

            /**
             * se estiver travado
             */
        }
        else
        {
            $('#plano_slider_two').cycle('prev');
            $('#plano_slider_preco_two').cycle('prev');
        }


        //
        return false;
    });

    $('.plano_next').on('click', function(e){
        e.preventDefault();

        if(!planoSliderOneLock)
        {
            $('#plano_slider_one').cycle('next').after(function(){
                $('#plano_slider_preco_one').cycle('next').after(function(){
                    $('#plano_slider_preco_two').cycle('next').after(function(){
                        $('#plano_slider_two').cycle('next').after(function(){})})})});
        }
        else
        {
            $('#plano_slider_two').cycle('next');
            $('#plano_slider_preco_two').cycle('next');
        }


        return false;
    });

    var qnt_cobertura = parseInt($('#quantidade_cobertura').val());
    var t_cobertura = parseInt($('#total_cobertura').val());
    for (i = qnt_cobertura+1; i <= t_cobertura; i++) {
        $( ".list_cobertura_"+i).hide();
    }

    $('.coberturas_ver_tudo_front').on('click', function(e){

        if($( ".coberturas_ver_tudo_front").hasClass('fundo-amarelo')){
            $( ".coberturas_ver_tudo_front").removeClass('fundo-amarelo');
            $( ".coberturas_ver_tudo_front").html('VER COBERTURAS');
            for (i = qnt_cobertura+1; i <= t_cobertura; i++) {
                $( ".list_cobertura_"+i).hide();
            }
        }else{
            $( ".coberturas_ver_tudo_front").addClass('fundo-amarelo');
            for (i = qnt_cobertura+1; i <= t_cobertura; i++) {
                $( ".list_cobertura_"+i).show();
                $( ".coberturas_ver_tudo_front").html('ESCONDER COBERTURAS');
            }
        }

        e.preventDefault();
        return false;
    });

    var altura_cobertura = $('#quantidade_cobertura').val();
    $( "#vendas_planos" ).height((28*altura_cobertura)+41);
    $( "#vendas_coberturas" ).height((28*altura_cobertura)+41);

    //
    if($('#total_cobertura').val() == 0) {
        $('.carrossel-left').removeClass('col-xs-6');
        $('.carrossel-left').addClass('col-xs-3');
        $('.carrossel-right').attr('style', 'padding-left: 18px;');
    }

    $('.coberturas_ver_tudo').on('click', function(e){


        var altura_cobertura = $('#quantidade_cobertura').val();
        var altura_cobertura = ((28*altura_cobertura)+41) + 'px';
        var total_cobertura = $('#total_cobertura').val();
        var total_cobertura = ((28*total_cobertura)+41) + 'px';

        console.log('altura_cobertura', altura_cobertura,  total_cobertura);

        if( $( "#vendas_coberturas").hasClass('open')){

            $('.coberturas_ver_tudo').removeClass('fundo-amarelo');
            $('.coberturas_ver_tudo').html('Ver todas as coberturas');

            $( "#vendas_coberturas" ).animate({
                height: altura_cobertura
            }, 500, function() {
                $( "#vendas_coberturas").removeClass('open');
            });
        }else {
            $('.coberturas_ver_tudo').addClass('fundo-amarelo');
            $('.coberturas_ver_tudo').html('Esconder as coberturas');
            $( "#vendas_coberturas" ).animate({
                height: total_cobertura
            }, 500, function() {
                $( "#vendas_coberturas").addClass('open');
            });
        }

        if( $( "#vendas_planos").hasClass('open')){
            $( "#vendas_planos" ).animate({
                height: altura_cobertura
            }, 500, function() {
                $( "#vendas_planos").removeClass('open');
            });
        }else {
            $( "#vendas_planos" ).animate({
                height: total_cobertura
            }, 500, function() {
                $( "#vendas_planos").addClass('open');
            });
        }

        e.preventDefault();
        return false;
    });
    function applyClickCobertura(){


        $('.btn_add_cobertura').on('click', function(e){

            var cobertura_id = $(this).attr('href').substr(1);

            /**
             * Adiciona ou remove a cobertura
             */
            if($(this).hasClass('has_cobertura')){

                $(this).removeClass('has_cobertura');
                $('.' + cobertura_id).removeClass('has_cobertura');

            }else {

                $(this).addClass('has_cobertura');
                $('.' + cobertura_id).addClass('has_cobertura');
            }
            /**
             * Verificar se foi adicionado alguma cobertura
             * e esconde/exibir o botão recalcular
             */
            if($(".has_cobertura").length){

                $( "#btn_recalcular").show( 600 );



            }else {
                $( "#btn_recalcular").hide( 600 );

            }

            e.preventDefault();
            return false;
        });
    }

    applyClickCobertura();

    function reloadCoberturaMark(){

        $( ".has_cobertura" ).each(function( index ) {

            var cobertura_id = $(this).attr('href').substr(1);
            $('.' + cobertura_id).addClass('has_cobertura');
        });
    }

    function applyUniform(){

        if ($('.uniformjs').length) $('.uniformjs').find(":checkbox, :radio").uniform();
    }
    applyUniform();

    function applyChangeContratar(){
        $( ".contratar_plano" ).change(function() {

            var plano_class = '.' + $(this).data('plano-class');
            if(this.checked){

                $( plano_class ).each(function( index ) {

                    $( this ).attr( "checked", true );
                });

            }else {

                $( plano_class ).each(function( index ) {

                    $( this ).attr( "checked", false );
                    console.log(this);
                });
            }
            //  $.uniform.update();
        });
    }
    applyChangeContratar();


    function reloadChangeContratar(){

        $( ".contratar_plano" ).each(function( index ) {
            var plano_class = '.' + $(this).data('plano-class');

            if(this.checked){
                console.log(this);
                $( plano_class ).each(function( index ) {

                    $( this ).attr( "checked", true );
                });
            }
        });
    }


    $('.add-car').on('click',function()
    {
        clear_carrinho();
        var plano = $(this).data('plano');
        var planos = $('#plano').val().split(';');

        var index = $.inArray(plano.toString(), planos);
        console.log(plano.toString(), planos);
        if($.inArray(plano.toString(), planos) > -1)
        {
            $('.delete-carrinho[data-plano="' + plano.toString() + '"]').trigger('click');
        }

        var plano = $(this).data('plano');
        var planos = $('#plano').val();

        if(planos.length == 0)
        {
            planos = [];
        }
        else
        {
            planos = planos.split(';');
        }

        planos.push(plano);
        $('#plano').val(planos.join(';'));

        var pass = $('#num_passageiro').val();
        if(pass.length == 0){
            pass = [];
        }else{
            pass = pass.split(';');
        }


        pass.push($('.num_passageiro').val());
        $('#num_passageiro').val(pass.join(';'));


        var nome = $('#plano_nome').val();
        if(nome.length == 0){
            nome = [];
        }else{
            nome = nome.split(';');
        }

        nome.push($('.plano_nome_one_'+plano).html());
        $('#plano_nome').val(nome.join(';'));


        var valor = $('#valor').val();
        if(valor.length == 0){
            valor = [];
        }else{
            valor = valor.split(';');
        }


        var coberturas_adicionas = []
        $('.ck-cobertura-adicional:checked').each(function() {
            var cobertura = $(this).val().split(';');
            if(cobertura[0] == plano){
                coberturas_adicionas.push(cobertura[1]);
            }
        });

        $('#cobertura_adicional').val(coberturas_adicionas.join(';'));
        $('#cobertura_adicional_valor_total').val($('.valor_cobertura_adicional_one_'+plano).html());
        $('#cobertura_adicional_valor').val($('#cobertura_adicional_valores_one_'+plano).val());

        valor.push($('.premio_bruto_one_'+plano).html());
        $('#valor').val(valor.join(';'));


        var desconto = $('#comissao_repasse').val();
        if(desconto.length == 0){
            desconto = [];
        }else{
            desconto = desconto.split(';');
        }

        if($('.repasse_comissao').val().length == 0) {
            desconto.push('00,00');
        }else{
            desconto.push($('.repasse_comissao').val());
        }
        $('#comissao_repasse').val(desconto.join(';'));


        var desconto_cond = $('#desconto_condicional').val();
        if(desconto_cond.length == 0){
            desconto_cond = [];
        }else{
            desconto_cond = desconto_cond.split(';');
        }

        if($('.desconto_condicional').val().length == 0) {
            desconto_cond.push('00,00');
        }else{
            desconto_cond.push($('.desconto_condicional').val());
        }
        $('#desconto_condicional').val(desconto_cond.join(';'));


        var desconto_cond_valor = $('#desconto_condicional_valor').val();
        if(desconto_cond_valor.length == 0){
            desconto_cond_valor = [];
        }else{
            desconto_cond_valor = desconto_cond_valor.split(';');
        }

        if($('.desconto_condicional_valor').val().length == 0) {
            desconto_cond_valor.push('0.00');
        }else{
            desconto_cond_valor.push($('.desconto_condicional_valor').val());
        }
        $('#desconto_condicional_valor').val(desconto_cond_valor.join(';'));


        var valor_total = $('#valor_total').val();
        if(valor_total.length == 0){
            valor_total = [];
        }else{
            valor_total = valor_total.split(';');
        }

        valor_total.push($('.premio_total_one_'+plano ).html());
        $('#valor_total').val(valor_total.join(';'));


        //Adiciona carrinho
        toastr.success("O item foi adicionado ao carrinho com sucesso!");

        window.scroll({
            top: 5000,
            left: 0,
            behavior: 'smooth'
        });


        updateCarrinho();

    });



    function updateCarrinho(){




        var tr_vazio = '<tr><td colspan="5">Seu Carrinho esta vazio</td></tr>';
        $('.body-carrinho tr').remove();

        var plano = $('#plano').val().split(';');
        if(plano.length == 0) {
            $('.body-carrinho').append(tr_vazio);
        }else {
            for (i = 0; i < plano.length; i++) {
                var nome = $('#plano_nome').val().split(';');
                var pass = $('#num_passageiro').val().split(';');
                var valor_total = $('#valor_total').val().split(';');
                var tr = '<tr class="plano-carrinho-'+ plano[i] +'">';
                tr += '<td>'+ (i+1) +'</td>';
                tr += '<td>'+ nome[i] +'</td>';
                tr += '<td>'+ pass[i] +'</td>';
                tr += '<td>'+ valor_total[i] +'</td>';
                tr += '<td><a href="javascript:void(0);" data-plano="'+ plano[i] +'" class="btn btn-sm btn-danger delete-carrinho"><i class="fa fa-eraser"></i>Excluir</a></td>';
                tr += '</tr>';
                $('.body-carrinho').append(tr);

                $('#produto_parceiro_plano_id').val( plano[i] );

            }
        }

        $('.delete-carrinho').on('click',function() {

            var plano = $(this).data('plano');
            var nome = $('#plano_nome').val().split(';');
            var pass = $('#num_passageiro').val().split(';');
            var valor = $('#valor').val().split(';');
            var valor_total = $('#valor_total').val().split(';');
            var desconto = $('#comissao_repasse').val().split(';');
            var desconto_cond = $('#desconto_condicional').val().split(';');

            planos = $('#plano').val().split(';');
            idx = $.inArray(plano.toString(), planos);

            if(idx != -1){
                planos.splice(idx, 1);
                nome.splice(idx, 1);
                pass.splice(idx, 1);
                valor.splice(idx, 1);
                valor_total.splice(idx, 1);
                desconto.splice(idx, 1);
                desconto_cond.splice(idx, 1);

                $('#plano').val(planos.join(';'));
                $('#plano_nome').val(nome.join(';'));
                $('#num_passageiro').val(pass.join(';'));
                $('#valor').val(valor.join(';'));
                $('#valor_total').val(valor_total.join(';'));
                $('#comissao_repasse').val(desconto.join(';'));
                $('#desconto_condicional').val(desconto_cond.join(';'));

                $('.body-carrinho tr.plano-carrinho-'+plano ).remove();

                var tr_vazio = '<tr><td colspan="5">Seu Carrinho esta vazio</td></tr>';
                var plano = $('#plano').val();
                if(plano.length == 0) {
                    $('.body-carrinho').append(tr_vazio);
                }

                $('#produto_parceiro_plano_id').val(0);

            }


        });

    }


    $('.btn_dados_segurado').on('click',function() {
        $('#validateSubmitForm').submit();
    });

    $('.btn-salvar-cotacao-modal').on('click',function() {
        $('#salvar_cotacao').val('1');
        $('#salvar_motivo').val($('#motivo').val());
        $('#salvar_motivo_ativo').val($('#motivo_ativo').val());
        $('#salvar_motivo_obs').val($('#motivo_obs').val());
        $('#validateSubmitForm').submit();
    });

    $('.delete-carrinho').on('click',function() {

        var plano = $(this).data('plano');
        var nome = $('#plano_nome').val().split(';');
        var pass = $('#num_passageiro').val().split(';');
        var valor = $('#valor').val().split(';');
        var valor_total = $('#valor_total').val().split(';');
        var desconto = $('#comissao_repasse').val().split(';');
        var desconto_cond = $('#desconto_condicional').val().split(';');

        planos = $('#plano').val().split(';');
        idx = $.inArray(plano.toString(), planos);

        if(idx != -1){
            planos.splice(idx, 1);
            nome.splice(idx, 1);
            pass.splice(idx, 1);
            valor.splice(idx, 1);
            valor_total.splice(idx, 1);
            desconto.splice(idx, 1);
            desconto_cond.splice(idx, 1);

            $('#plano').val(planos.join(';'));
            $('#plano_nome').val(nome.join(';'));
            $('#num_passageiro').val(pass.join(';'));
            $('#valor').val(valor.join(';'));
            $('#valor_total').val(valor_total.join(';'));
            $('#comissao_repasse').val(desconto.join(';'));
            $('#desconto_condicional').val(desconto_cond.join(';'));

            $('.body-carrinho tr.plano-carrinho-'+plano ).remove();

            var tr_vazio = '<tr><td colspan="5">Seu Carrinho esta vazio</td></tr>';
            var plano = $('#plano').val();
            if(plano.length == 0) {
                $('.body-carrinho').append(tr_vazio);
            }

            $('#produto_parceiro_plano_id').val(0);

        }


    });

    function clear_carrinho(){

        $('#plano').val('');
        $('#plano_nome').val('');
        $('#num_passageiro').val('');
        $('#valor').val('');
        $('#valor_total').val('');
        $('#comissao_repasse').val('');
        $('#desconto_condicional').val('');

        var tr_vazio = '<tr><td colspan="5">Seu Carrinho esta vazio</td></tr>';

        $('.body-carrinho tr' ).remove();
        $('.body-carrinho').append(tr_vazio);

        $('#produto_parceiro_plano_id').val(0);

    }

    $(".carousel").swipe({

        swipe: function(event, direction, distance, duration, fingerCount, fingerData) {
            if (direction == 'left') $(this).carousel('next');
            if (direction == 'right') $(this).carousel('prev');
        },
        allowPageScroll:"vertical"

    });

});
