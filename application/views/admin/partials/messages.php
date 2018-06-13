<?php if($this->session->flashdata('warn_msg')): ?>
    <div class="alert alert-danger alert-dismissable">
        <i class="fa fa-ban"></i>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <b>Alert!</b>  <?php echo $this->session->flashdata('warn_msg');?>
    </div>
<?php endif;?>
<?php if($this->session->flashdata('info_msg')): ?>
    <div class="alert alert-info fade in">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <i class="fa fa-info-circle"></i> <?php echo $this->session->flashdata('info_msg');?>
    </div>
<?php endif;?>
<?php if($this->session->flashdata('succ_msg')): ?>
    <div class="alert alert-success fade in widget-inner">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <i class="fa fa-check"></i> <?php echo $this->session->flashdata('succ_msg');?>
    </div>
<?php endif;?>
<?php if($this->session->flashdata('fail_msg')): ?>
    <div class="alert alert-danger fade in widget-inner">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <i class="fa fa-times"></i> <?php echo $this->session->flashdata('fail_msg');?>
    </div>
<?php endif;?>


