
    $(window).load(function(){

        if (navigator.userAgent.toLowerCase().indexOf("chrome") >= 0) {

            $('input:-webkit-autofill').each(function () {
                var text = $(this).val();
                var name = $(this).attr('name');
                console.log(text, name);
                $(this).after(this.outerHTML).remove();
                $('input[name=' + name + ']').val(text);
            });
        }
    });

    $(function() {
        $('#modal-termo_aceite').modal();
    });
