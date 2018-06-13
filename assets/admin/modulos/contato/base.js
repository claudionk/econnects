$(document).ready(function(){
    set_contato();
    $('.tipo_contato').change(function() {
        set_contato();
    });



});

function set_contato(){

    switch($('.tipo_contato').val()) {
        case '1': //email
            $('.contato').inputmask('remove');
            $(".contato").attr("placeholder", "");
            break;
        case '2':
            $(".contato").inputmask("mask", {"mask": "(99)9999-99999"});
            $(".contato").attr("placeholder", "(__)____-_____");
            break;
        case '3':
        case '4':
            $(".contato").inputmask("mask", {"mask": "(99)9999-9999"});
            $(".contato").attr("placeholder", "(__)____-____");
            break;
        default:
            $('.contato').inputmask('remove');
            $(".contato").attr("placeholder", "");

    }
}


