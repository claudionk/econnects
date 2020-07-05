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

    $('#modal_gerar_chave').on('show.bs.modal', function (e) {
        $(this).find(".modal-content").html('Carregando ...');
        var link = $(e.relatedTarget);
        $(this).find(".modal-content").load(link.data("href"));
        $('#inp_gerar_chave').select().focus();
    });

    $(document).on("click", ".modalGerarChave" , function(e)
    {
        if ( !($('#parceiro_id').val() > 0) )
        {
            toastr.info("Informe a Empresa");
            $('#parceiro_id').focus();
            return;
        }

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

        $(this).attr('disabled', true).text('Aguarde .... ');
        $.blockUI({ message: null });
        $('#formGerarChave').submit();
    });
});
