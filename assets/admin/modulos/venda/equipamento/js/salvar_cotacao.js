$(function(){

    /**
     * Adiciona contato
     */
    $('.btAdicionarContato').click(function()
    {
        var val_radio =  $("input[name='cliente_terceiro[0]']:checked").val();
        $(this).closest("div.contato").clone().attr("copia", true).insertAfter($(this).attr('insert'));


        //
        atualiza_delete();
        adicionarContato();
        if (typeof val_radio != 'undefined')
            $("input[name='cliente_terceiro[0]']").filter('[value='+ val_radio +']').prop('checked', true);
    });

    function adicionarContato()
    {
        var id = ""
        var quant_itens =  $("#quantidade_contatos").val();
        $("div.contato[copia='true'] input,  div.contato[copia='true'] select").each(function(){
            var name = $(this).attr("name").toString();
            var ini = name.indexOf('[');
            var newName = name.substring(0, ini + 1) + quant_itens + "]";
            var newID = name.substring(0, ini) + '_' +quant_itens;

            $(this).attr('name', newName);
            $(this).attr('id', newID);
            $(this).removeAttr("disabled");
            if($(this).attr('type') != 'radio') {
                $(this).val("");
            }
            if(name.substring(0, ini) == 'contato'){
                //set_contato(this);
                $(this).inputmask('remove');
                $(this).attr("placeholder", "");

            }
        });
        $(" div.contato[copia='true'] input,  div.contato[copia='true']").find(".tipos_de_contato, .opcoes, .btAdicionarContato").remove();
        $(" div.contato[copia='true'] input,  div.contato[copia='true']").find(".btAdicionarContato").remove();
        $(" div.contato[copia='true'] input,  div.contato[copia='true']").find(".btRetirarContato").removeClass("hidden");

        $(" div.contato[copia='true'] input,  div.contato[copia='true']").find(".has-error").removeClass("has-error");
        $(" div.contato[copia='true'] .help-block").remove();

        $("div.contato[copia='true']").removeAttr("copia");

        quant_itens++;
        $("#quantidade_contatos").val(quant_itens);
    }

    function atualiza_delete()
    {
        $(".btRetirarContato").click(function()
        {
            $(this).closest("div.contato").remove();
            var quant_itens =  $("#quantidade_contatos").val();
            quant_itens--;
            $("#quantidade_contatos").val(quant_itens);
        })

        $("input:radio").click(function() {
            var ini = $(this).attr("name").toString().indexOf('[');
            var fim = $(this).attr("name").toString().indexOf(']');
            var item = $(this).attr("name").toString().substring(ini+1, fim);


            if($(this).val() == 1){
                $('#contato_nome_'+ item).removeAttr("disabled");
                $('#contato_nome_'+ item).val('');
                $('#contato_nome_'+ item).focus();
            }else{
                $('#contato_nome_'+ item).attr("disabled", true);
                $('#contato_nome_'+ item).val($('#nome_segurado').val());
            }
        });

        $('.tipo_contato').change(function() {
            set_contato(this);
        });

    }

    //
    function set_contato(campo){

        var ini = $(campo).attr("name").toString().indexOf('[');
        var fim = $(campo).attr("name").toString().indexOf(']');
        var item = $(campo).attr("name").toString().substring(ini+1, fim);

        switch($(campo).val()) {
            case '1': //email
                $('#contato_' + item).inputmask('remove');
                $('#contato_' + item).attr("placeholder", "");
                break;
            case '2':
                $('#contato_' + item).inputmask("mask", {"mask": "(99)9999-99999"});
                $('#contato_' + item).attr("placeholder", "(__)____-_____");
                break;
            case '3':
            case '4':
                $('#contato_' + item).inputmask("mask", {"mask": "(99)9999-9999"});
                $('#contato_' + item).attr("placeholder", "(__)____-____");
                break;
            default:
                $('#contato_' + item).inputmask('remove');
                $('#contato_' + item).attr("placeholder", "");

        }
    }

    $("select").each(function(){
       var ini = $(this).attr("name").toString().indexOf('[');
        console.log($(this).attr("name").toString().substring(0, ini));
        if($(this).attr("name").toString().substring(0, ini) == 'contato_tipo_id'){
            set_contato(this);
        }

    });


    atualiza_delete();
})