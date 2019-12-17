
<?php $this->load->view('admin/venda/equipamento/front/head', array('step' => 2, 'produto_parceiro_id' =>  issetor($produto_parceiro_id), 'title' => '')); ?>

<div class="row">
    <div class="col-md-12"><?php $this->load->view('admin/partials/messages'); ?></div>
</div>

<div>
        <div class="row">
            <div class="col-xs-3">
                <span class="span-prod">Celular</span>       
            </div>
            <div class="col-xs-9">     
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <a href="<?php echo base_url("$current_uri/index")?>"><div class="btn-prod"><span class="glyphicon glyphicon-picture"></span></div></a>
            </div>
            <div class="col-xs-9">
                <div class="row">
                     <span class="span-prod">Maffre</span> 
                </div>  
                <div class="row">
                     <span class="span-">VIGÊNCIA:</span> 10/12/2019 a 10/12/2020
                </div> 
                <div class="row">
                     <span class="span-">APÓLICE:</span> R$ 2.000,00
                </div>  
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <a href="<?php echo base_url("$current_uri/index")?>">VISUALIZAR</a>      
            </div>
            <div class="col-xs-9">     
            </div>
        </div>
</div>

<div>
        <div class="row" style="padding-top:20px">
            <div class="col-xs-3">
                <span class="span-prod">Geladeira</span>       
            </div>
            <div class="col-xs-9">     
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <a href="<?php echo base_url("$current_uri/index")?>"><div class="btn-prod"><span class="glyphicon glyphicon-picture"></span></div></a>
            </div>
            <div class="col-xs-7">
                <div class="row">
                     <span class="span-prod">Generalli</span> 
                </div>  
                <div class="row">
                     <span class="span-">VENCIMENTO:</span> 20/12/2020
                </div> 
                <div class="row">
                     <span class="span-">APÓLICE:</span> R$ 8.000,00
                </div>  
            </div>
            <div class="col-xs-2" style="padding-top:20px; font-size: 30px;">
                <a href="<?php echo base_url("$current_uri/index")?>"><span class="glyphicon glyphicon-plus-sign"></span></a>
            </div>
        </div>
        
</div>

