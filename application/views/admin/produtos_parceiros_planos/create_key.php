<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Gerar Chaves para o plano <?php echo $row['nome'] ?></h4>
</div>
<div class="modal-body explicacao_modal">
    <div class="row">

        <form class="form-horizontal margin-none" id="formGerarChave" method="post" autocomplete="off" action="<?php echo base_url("{$current_controller_uri}/keyCreate/{$produto_parceiro_id}/{$row['produto_parceiro_plano_id']}")?>">
            <div class="form-group">
                <div class="text-col text-uppercase">Informe a Empresa</div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    <select class="form-control" name="parceiro_id" id="parceiro_id">
                        <option value="">Selecione</option>
                        <?php
                        foreach($empresas as $emp) { ?>
                            <option value="<?php echo $emp['parceiro_id'] ?>"> <?php echo $emp['nome_fantasia']; ?> </option>
                        <?php }  ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="text-col text-uppercase">Informe a quantidade de chaves que deseja gerar</div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    <input class="form-control" id="inp_gerar_chave" name="inp_gerar_chave" type="text" placeholder="Ex: 100" />
                    <input type="hidden" name="id" id="id" value="<?php echo $row['produto_parceiro_plano_id'] ?>" />
                </div>
            </div>
        </form>

    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-primary modalGerarChave" > Gerar</button>
    <button type="button" class="btn btn-default modalCloseGerarChave" data-dismiss="modal"><i class="fa fa-close"></i> Fechar</button>
</div>
