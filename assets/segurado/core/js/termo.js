
    $(window).load(function(){

        $('#modal-termo_aceite').modal();

    });


    $('#modal-termo_aceite').on('hidden.bs.modal', function () {
        document.location.reload();
    })

