<form action="<?php echo admin_url("produtos_parceiros_planos_precificacao_servico/importar_excel/{$produto_parceiro_plano_id}") ?>" method="post" enctype="multipart/form-data">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Carregar excel</h4>
        </div>
        <div class="modal-body">
            <p>Selecione o arquivo excel para substituição da preficiação.</p>

            <div class="form-group">
                <input type="file" name="arquivo">
                <label>Arquivo excel</label>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Importar</button>
        </div>
    </div>

</form>