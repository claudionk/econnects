
$(function()
{
// Basic


    $("#select2").select2();

    $(".btn-salvar-cotacao").on("click", function() {
        $('#salvar_cotacao').val('1');
        $('#validateSubmitForm').submit();
    });
});