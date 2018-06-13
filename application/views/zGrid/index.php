<form name="form_<?php echo (isset($grid['grid_id'])) ? $grid['grid_id'] : 'form_zGrid'?>"
      id="form_<?php echo (isset($grid['grid_id'])) ? $grid['grid_id'] : 'form_zGrid'?>"
      method="<?php echo (isset($grid['form']['method'])) ?$grid['form']['method'] : 'POST'?>"
      enctype="<?php echo (isset($grid['form']['enctype'])) ?$grid['form']['enctype'] : 'multipart/form-data'?>"
      action="<?php echo (isset($grid['form']['action'])) ? $grid['form']['action'] : ''?>">

    <input type="hidden" name="__action" id="__action" value="">
    <?php if ($grid['actions_footer']): ?>
        <div class="row">
            <div class="col-sm-12">
                <?php echo $grid['actions_footer']; ?>
            </div>
        </div>
    <?php endif;  ?>
    <div class="row"><br/></div>
    <table id="<?php echo (isset($grid['grid_id'])) ? $grid['grid_id'] : 'zGrid'?>"
           class="table table-striped table-bordered dataTable <?php echo (isset($grid['class'])) ? $grid['class'] : ''?>"
           cellspacing="<?php echo (isset($grid['cellspacing'])) ? $grid['cellspacing'] : '0'?>"
           width="<?php echo (isset($grid['width'])) ? $grid['width'] : '100%'?>">
        <?php $this->load->view('zGrid/head', array('grid' => $grid)); ?>
        <?php $this->load->view('zGrid/foot', array('grid' => $grid)); ?>
    </table>
    <?php if ($grid['actions_footer']): ?>
        <div class="row"><br/></div>
        <div class="row">
            <div class="col-sm-12">
                <?php echo $grid['actions_footer']; ?>
            </div>
        </div>
    <?php endif;  ?>
</form>
<script type="text/javascript">
    <?php echo (isset($grid['javascript'])) ? $grid['javascript'] : ''?>

    function delete_selected(){
        var rows_selected = __datatable.column(0).checkboxes.selected();

        if(rows_selected.length){
            $.each(rows_selected, function(index, rowId){

                $('#__action').val('delete');

                $('#form_<?php echo (isset($grid['grid_id'])) ? $grid['grid_id'] : 'form_zGrid'?>').append(
                    $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', 'selected[]')
                        .val(rowId['<?php echo $grid['key']?>'])
                );
            });
            $('#form_<?php echo (isset($grid['grid_id'])) ? $grid['grid_id'] : 'form_zGrid'?>').submit();
        }
    }
</script>
