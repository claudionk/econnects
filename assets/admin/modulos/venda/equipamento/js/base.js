
$(function()
{
// Basic


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
    })
});