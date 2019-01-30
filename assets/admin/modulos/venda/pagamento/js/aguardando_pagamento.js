$(document).ready(function(){

    $('#widget-progress-bar .progress-bar').width("30%");
    getStatusPedido();

/*
    p._initDatePicker = function () {
        if (!$.isFunction($.fn.datepicker)) {
            return;
        }

        
    }; */

});

function abreModalDebito(){


    $('.box-debito').show();

    $('#modal-debito').modal();



}

var pr =30;
var ver_redirect = false;
var contador = 0;

function getStatusPedido(){
    var data = {
        pedido_id: $('#pedido_id').val( )
    }

    var url = $('#url_aguardando_pagamento').val();
    pr += 5;
    if(pr > 100){ pr = 30; }
    //    Pace.track(function(){
        $.ajax({
                type: "POST",
                url: url,
                cache: false,
                data: data,
            })
            .done(function( result ) {
                console.log(result);

                var msg_transacao = '';//result.transacao_message
                if(result.result == true){
                    $('#widget-progress-bar .steps-percent').html("100%");
                    $('#widget-progress-bar .progress-bar').width("100%");

                    if(result.status_id == 3){
                        $('.box-sucesso').show();
                        $('.box-debito').hide();
                       $('#modal-debito').hide();
                        $( ".close" ).trigger( "click" );
                    }

                    if(result.status_id == 4){
                        $('.box-erro').show();
                        $('.box-debito').hide();
                        $('#modal-debito').hide();
                        msg_transacao = result.transacao_message;
                    }

                    $('.title-pagamento').html(result.status_pedido);
                    $('.text-progress-bar').html(result.status_pedido);

                }
                else{

                    /*if(result.transacao_result == 'REDIRECT' && ver_redirect == false ){
                        ver_redirect = true;

                        $('.box-debito a').attr('href',  result.transacao_url);
                        $('.btn-debito').attr('href',  result.transacao_url);


                        document.location.href = result.transacao_url;


                        setTimeout(abreModalDebito, 10000);

                    } else if (result.status_slug == 'erro') {
                        // alert(result.status_pedido); 
                        if (contador == 20 || contador == 40 || contador == 60 || contador == 80)
                        {
                            $('#modal-falha').modal('show').on('hidden.bs.modal', function(){
                                if(contador > 80){
                                    document.location.href = 'http://sisconnects.com.br/admin/venda';
                                }
                            });
                        }
                    }

                    $('#widget-progress-bar .steps-percent').html(pr + "%");
                    $('#widget-progress-bar .progress-bar').width(pr + "%");
                    setTimeout(getStatusPedido, 2000);*/


                    if(result.transacao_result == 'REDIRECT' && ver_redirect == false ){
                        ver_redirect = true;

                        $('.box-debito a').attr('href',  result.transacao_url);
                        $('.btn-debito').attr('href',  result.transacao_url);


                        document.location.href = result.transacao_url;


                        setTimeout(abreModalDebito, 10000);

                    }
                    else{
                        if( result.status_slug == 'erro' ){
                            $('#modal-falha').modal('show').on('hidden.bs.modal', function(){
                                document.location.href = 'http://sisconnects.com.br/admin/venda';
                            });
                        }
                        else{
                            if( result.transacao_result == '' ){
                                $('#widget-progress-bar .steps-percent').html(pr + "%");
                                $('#widget-progress-bar .progress-bar').width(pr + "%");
                                setTimeout(getStatusPedido, 2000);
                            }
                            else{
                                result.status_pedido = result.transacao_message
                                msg_transacao = result.transacao_message
                                $('#widget-progress-bar .steps-percent').html("100%");
                                $('#widget-progress-bar .progress-bar').width("100%");
                            }
                        }
                    }

                }

                $('#btn-status').attr('class', '');
                $('#btn-status').addClass('btn');
                $('#btn-status').addClass(result.class_pagamento);
                $('#btn-status').html(result.status_pedido);
                $('.status-detalhe').html(msg_transacao);
            });
  //  });



}

