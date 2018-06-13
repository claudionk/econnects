var cartao;
var cartao_debito;

$(document).ready(function() {

    $('#validade').datepicker({autoclose: true, todayHighlight: true, format: "mm/yyyy", startDate: '0d', language: 'pt-BR', forceParse : false,  minViewMode: 1});
    $('#validade_debito').datepicker({autoclose: true, todayHighlight: true, format: "mm/yyyy", startDate: '0d', language: 'pt-BR', forceParse : false,  minViewMode: 1});


    cartao = $('#validateSubmitForm #pagamento-credito').card({
        // a selector or DOM element for the form where users will
        // be entering their information
        /*form: 'validateSubmitForm', */ // *required*
        // a selector or DOM element for the container
        // where you want the card to appear
        container: '.card-wrapper', // *required*

        formSelectors: {
            numberInput: 'input[name="numero"]', // optional — default input[name="number"]
            expiryInput: 'input[name="validade"]', // optional — default input[name="expiry"]
            cvcInput: 'input[name="codigo"]', // optional — default input[name="cvc"]
            nameInput: 'input[name="nome_cartao"]' // optional - defaults input[name="name"]
        },

        width: '350px', // optional — default 350px
        formatting: true, // optional - default true

        // Strings for translation - optional
        messages: {
            validDate: 'Validade', // optional - default 'valid\nthru'
            monthYear: 'mm/aaaa', // optional - default 'month/year'
        },

        // Default placeholders for rendered fields - optional
        placeholders: {
            number: '•••• •••• •••• ••••',
            name: 'Nome Completo',
            expiry: '••/••',
            cvc: '•••'
        },

        // if true, will log helpful messages for setting up Card
        debug: true // optional - default false
    });


    cartao_debito = $('#validateSubmitForm #pagamento-debito').card({
        // a selector or DOM element for the form where users will
        // be entering their information
        /*form: 'validateSubmitForm', */ // *required*
        // a selector or DOM element for the container
        // where you want the card to appear
        container: '.card-wrapper-debito', // *required*

        formSelectors: {
            numberInput: 'input[name="numero_debito"]', // optional — default input[name="number"]
            expiryInput: 'input[name="validade_debito"]', // optional — default input[name="expiry"]
            cvcInput: 'input[name="codigo_debito"]', // optional — default input[name="cvc"]
            nameInput: 'input[name="nome_cartao_debito"]' // optional - defaults input[name="name"]
        },

        width: '350px', // optional — default 350px
        formatting: true, // optional - default true

        // Strings for translation - optional
        messages: {
            validDate: 'Validade', // optional - default 'valid\nthru'
            monthYear: 'mm/aaaa', // optional - default 'month/year'
        },

        // Default placeholders for rendered fields - optional
        placeholders: {
            number: '•••• •••• •••• ••••',
            name: 'Nome Completo',
            expiry: '••/••',
            cvc: '•••'
        },

        // if true, will log helpful messages for setting up Card
        debug: true // optional - default false
    });

    $('input[name="bandeira"]').change(function() {
        $('.parcelamento').hide();
        $('.parcelamento_'+ $(this).val() ).show();
    });

    $('.w-forma-pagamento').click(function() {
        $('#forma_pagamento_tipo_id').val($(this).data('forma'));
    });


    //

});


function GetCardType(number)
{
    // visa
    var re = new RegExp("^4");
    if (number.match(re) != null)
        return "Visa";

    // Mastercard
    re = new RegExp("^5[1-5]");
    if (number.match(re) != null)
        return "Mastercard";

    // AMEX
    re = new RegExp("^3[47]");
    if (number.match(re) != null)
        return "AMEX";

    // Discover
    re = new RegExp("^(6011|622(12[6-9]|1[3-9][0-9]|[2-8][0-9]{2}|9[0-1][0-9]|92[0-5]|64[4-9])|65)");
    if (number.match(re) != null)
        return "Discover";

    // Diners
    re = new RegExp("^36");
    if (number.match(re) != null)
        return "Diners";

    // Diners - Carte Blanche
    re = new RegExp("^30[0-5]");
    if (number.match(re) != null)
        return "Diners - Carte Blanche";

    // JCB
    re = new RegExp("^35(2[89]|[3-8][0-9])");
    if (number.match(re) != null)
        return "JCB";

    // Visa Electron
    re = new RegExp("^(4026|417500|4508|4844|491(3|7))");
    if (number.match(re) != null)
        return "Visa Electron";

    return "";
}