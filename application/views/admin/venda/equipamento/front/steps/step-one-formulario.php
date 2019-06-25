<div>
    <!-- Form -->
    <form active="" class="form form-dados" id="validateSubmitForm" method="post" autocomplete="off" enctype="multipart/form-data">
        <input type="hidden" name="<?php echo $primary_key ?>" id="<?php echo $primary_key ?>" value="<?php if (isset($produto_parceiro_id)) echo $produto_parceiro_id; ?>"/>
        <input type="hidden" name="cotacao_id" value="<?php if (isset($cotacao_id)) echo $cotacao_id; ?>"/>
        <input type="hidden" id="url_busca_cliente"  name="url_busca_cliente" value="<?php echo base_url("{$current_controller_uri}/get_cliente"); ?>"/>

        <div class="row">
            <div class="col-md-6">
                <?php // $this->load->view('admin/partials/validation_errors');?>
                <?php $this->load->view('admin/partials/messages'); ?>
            </div>
        </div>

        <?php
        $this->load->view('admin/venda/equipamento/front/step', array('step' => 1, 'produto_parceiro_id' => $produto_parceiro_id, 'title' => 'DADOS INICIAIS'));    
        ?>

        <?php // $this->load->view('admin/venda/step', array('step' => 1 ,'produto_parceiro_id' =>  issetor($produto_parceiro_id))); ?>

        <?php $this->load->view('admin/campos_sistema/lista_campos'); ?>
    </form>
</div>

<div class="btns">
    <!-- // Widget END -->
    <?php if ($layout == "base") { ?>
        <a href="<?php echo base_url("{$current_controller_uri}/index")?>" class="btn  btn-app btn-primary">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>

        <a href="<?php echo base_url("admin/clientes/view/")?>" class="btn  btn-app btn-primary btn-detalhes disabled ls-modal">
            <i class="fa fa-chain"></i> Detalhes do Cliente
        </a>
    <?php } ?>

    <a class="btn btn-app btn-primary btn-proximo background-primary border-primary" onclick="$('#validateSubmitForm').submit();">
        Próximo <i class="fa fa-angle-right" aria-hidden="true"></i>
    </a>
</div>

<div id="detalhe-cliente" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Detalhes do Cliente</h4>
            </div>
            <div class="modal-body">
                <p>Carregando...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<?php if($this->template->get('layout') == 'front'){ ?>
<?php $this->load->view('admin/venda/equipamento/components/btn-whatsapp'); ?>

<?php $this->load->view('admin/venda/equipamento/components/footer'); ?>
<?php } ?>

<style style="text/css">
.inputmask-valor{
    text-align: center !important;
}
.btn.disabled, .btn[disabled], fieldset[disabled] .btn {
    width: 70%;
    background: #c41f1b;
    padding: 7px 10px;
    border-radius: 5px;
    text-transform: uppercase;
    font-size: 1.8rem;
    font-weight: 100;
    margin-bottom: 4rem;
    opacity: 1;
    color: #ffffff;
}
</style>
<script>
/*
$(document).ready(function(){
    // hidden all input
    var arrayDivs = [];

    $(".form-group").each(function(key, values){
        var divs = $(this)
        arrayDivs.push(divs)

        console.log(values)

        divs.css('display', 'none');
        
        $('.btn-proximo').attr('disabled', true)

        if(key == 0){
            divs.css('display', 'block');
        }
    });
    if($('#cnpj_cpf').length){
        $('#cnpj_cpf').focusout(function(){
            if($(this).val() != ''){
                arrayDivs[1].css('display', 'block')
                $('#nome').focus()
            }else{
                $(this).focus()
            }
        });
    }
    if($('#nome').length){
        $('#nome').focusout(function(){
            if($(this).val() != ''){
                arrayDivs[2].css('display', 'block')
                $('#email').focus()
            }else{
                $(this).focus()
            }
        });
    }
    if($('#email').length){
        $('#email').focusout(function(){
            if($(this).val() != ''){
                arrayDivs[3].css('display', 'block')
                $('#telefone').focus()
            }else{
                $(this).focus()
            }
        });
    }
    if($('#telefone').length){
        $('#telefone').focusout(function(){
            if($(this).val() != ''){
                arrayDivs[4].css('display', 'block')
                $('#data_nascimento').focus()
            }else{
                $(this).focus()
            }
        });
    }
    if($('#data_nascimento').length){
        $('#data_nascimento').focusout(function(){
            if($(this).val() != ''){
                arrayDivs[5].css('display', 'block')
                $('#rg_data_expedicao').focus()
            }
        });
    }
    if($('#rg_data_expedicao').length){
        $('#rg_data_expedicao').focusout(function(){
            if($(this).val() != ''){
                arrayDivs[6].css('display', 'block')
                arrayDivs[7].css('display', 'block')
                $('#ean').focus()
            }
        });
    }
    if($('#ean').length){
        $('#ean').focusout(function(){
            arrayDivs[8].css('display', 'block')
            arrayDivs[9].css('display', 'block')
            arrayDivs[10].css('display', 'block')
            arrayDivs[11].css('display', 'block')
            arrayDivs[12].css('display', 'block')
            $('.btn-proximo').attr('disabled', false)
        });
    }
});
*/
</script>