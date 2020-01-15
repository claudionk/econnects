<?php $this->load->view('admin/venda/equipamento/front/head', array('step' => 2, 'produto_parceiro_id' =>  issetor($produto_parceiro_id), 'title' => '')); ?>

<div class="row">
    <div class="col-md-12"><?php $this->load->view('admin/partials/messages'); ?></div>
</div>


<div style="margin-left: 3%; margin-right: 3%;">
    <a id="menu-close" href="#" class="btn btn-default btn-lg pull-right toggle">&times;</a>
<div class="row">
    <div class="col-xs-6">
            <a href="<?php echo base_url("$current_uri/index")?>">
                <div class="btn-prod">
                    <div class="row">
                        <img height="45px" src="<?php echo base_url(); ?>assets/admin/core/images/icons/cel.png"/>
                    </div>
                    <div class="row">
                        <span class="span-prod">Celular</span>
                    </div>
                </div>
            </a>
    </div>
    <div class="col-xs-6">
            <a href="<?php echo base_url("$current_uri/index")?>">
                <div class="btn-prod">
                    <div class="row">
                        <img height="45px" src="<?php echo base_url(); ?>assets/admin/core/images/icons/smartwatch.png"/>
                    </div>
                    <div class="row">
                        <span class="span-prod">SmartWacth</span>
                    </div>
                </div>
            </a>
    </div>
</div>

<div class="row">
    <div class="col-xs-6">
            <a href="<?php echo base_url("$current_uri/index")?>">
                <div class="btn-prod">
                    <div class="row">
                        <img height="45px" src="<?php echo base_url(); ?>assets/admin/core/images/icons/computer.png"/>
                    </div>
                    <div class="row">
                        <span class="span-prod">Notebook</span>
                    </div>
                </div>
            </a>
    </div>


    <div class="col-xs-6">
            <a href="<?php echo base_url("$current_uri/index")?>">
                <div class="btn-prod">
                    <div class="row">
                        <img height="45px" src="<?php echo base_url(); ?>assets/admin/core/images/icons/cam.png"/>
                    </div>
                    <div class="row">
                        <span class="span-prod">Filmadoras</span>
                    </div>
                </div>
            </a>
    </div>
</div>

<div class="row">
    <div class="col-xs-6">
            <a href="<?php echo base_url("$current_uri/index")?>">
                <div class="btn-prod">
                    <div class="row">
                        <img height="45px" src="<?php echo base_url(); ?>assets/admin/core/images/icons/cam2.png"/>
                    </div>
                    <div class="row">
                        <span class="span-prod">Câmeras</span>
                    </div>
                </div>
            </a>
    </div>
    <div class="col-xs-6">
            <a href="<?php echo base_url("$current_uri/index")?>">
                <div class="btn-prod">
                    <div class="row">
                        <img height="45px" src="<?php echo base_url(); ?>assets/admin/core/images/icons/tablet.png"/>
                    </div>
                    <div class="row">
                        <span class="span-prod">Tablet</span>
                    </div>
                </div>
            </a>
    </div>
</div>  
</div>
