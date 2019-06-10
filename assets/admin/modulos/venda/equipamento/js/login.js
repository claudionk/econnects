
$(function() {

    $('#btnFormLogin').click(function(){

        if( $('#password').val() == '' ) {
            showMessage('Senha inválida');
            return false;
        }
        if( $('#password_confirm').val() == '' ) {
            showMessage('Confirmação de senha inválida');
            return false;
        }
        if( $('#password').val() != $('#password_confirm').val() ){
            showMessage('As senhas não são iguais');
            return false;
        }

        $('#formLogin').submit()
    })
});
function showMessage(message){
    $('.toast-message').html( message )

    $('.message').fadeIn("slow")

    $('#password').val('')
    $('#password_confirm').val('')

    $('#password').focus()


    $('.message').fadeOut(5000)
}