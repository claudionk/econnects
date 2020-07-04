function salvarOrdem()
{
    var i = 0;
    var a = new Array();
    $("#tabela-ordem > tbody > tr").each(function(index ) {
        a[i] = new Array();
        a[i][0] = $(this).data('id');
        a[i][1] = $(this).data('ordem');
        i++;
    });

    url = $('#url_ordem').val();

    $.ajax({
            type: "POST",
            url: url,
            cache: false,
            data: {itens: a}
        })
        .done(function( result ) {
            location.reload();
        });

}

$(document).ready(function(){

    $('#tabela-ordem').tableDnD({
        onDragClass: "drag",
    });

    $(".salvar-ordem").click(function () {
        salvarOrdem();
    });

    $(".campo_tipo").change(function () {
        url = $('#url_tipo').val() + '/' + $(".campo_tipo").val();
        document.location.href = url;
    });

    $('#modal_gerar_chave').on('shown.bs.modal', function (e) {
        $('#id').val( $(e.relatedTarget).data('id') );
        var plano = $('#ppp_id_'+ $('#id').val()).text();
        $(' .modal-title', this).text( 'Gerar Chave para o plano '+ plano );
        $('#inp_gerar_chave').select().focus();
    });

    $('#modalGerarChave').click(function()
    {
        if ( !Number.isInteger(parseInt($('#inp_gerar_chave').val())) )
        {
            toastr.info("A Quantidade de Chave deve ser numérica.");
            $('#inp_gerar_chave').focus();
            return;
        }

        if ( !($('#inp_gerar_chave').val() > 0) )
        {
            toastr.info("A Quantidade de Chave deve ser maior que zero.");
            $('#inp_gerar_chave').focus();
            return;
        }

        $(this).attr('disabled', true).text('Aguarde ...');

        var actForm = $('#formGerarChave').attr("action");
        $('#formGerarChave')
            .attr("action", actForm +'/'+ $('#id').val() )
            .submit();
    });
});
