

$(function(){

    var produto_parceiro_id = $("#produto_parceiro_id").val();

    $("#bt_enviar_email").click(function(){
        handle_dispara_evento_url(this, "email");
    })

    $("#bt_enviar_sms").click(function(){
        handle_dispara_evento_url(this, "sms");
    })

    function handle_dispara_evento_url($dom, tipo){

        var acao = "pagamento";

        if($dom.dataset){
            if($dom.dataset.acao){
                acao = $dom.dataset.acao;
            }
        }

        if (acao == "pagamento") {
            dispara_evento_url_pagamento(tipo);
        } else if (acao == "cancelamento") {
            dispara_evento_url_cancelamento(tipo);
        } 

    }

    function dispara_evento_url_cancelamento(tipo) {
        dispara_evento_url("cancelamento", tipo);
    }

    function dispara_evento_url_pagamento (tipo) {
        dispara_evento_url("pagamento", tipo);
    }

    function dispara_evento_url(acao, tipo) {
        $.ajax(base_url + "admin/venda/envia_comunicacao_url_"+acao+"/", {
            data: {
                produto_parceiro_id : produto_parceiro_id,
                contato: $("#" + tipo).val(),
                url_acesso_externo: $("#url_acesso_externo").val(),
                nome: $("#nome_contato").val(),
                tipo: tipo,
            },
            dataType: 'json',
            type:'POST',
            success: function(data)
            {
                if(data.status)
                {
                    toastr.success("Sucesso ao enviar a comunicação.")
                    $("#modal_trocar_cartao").modal("hide");
                }
                else
                {
                    toastr.error(data.mensagem)
                }
            }
        })
    }
})