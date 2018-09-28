<?php if($row['comissao_config']) : ?>
<?php $field_name = 'markup';?>
<div class="form-group">
  <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Markup(%) *</label>
  <div class="col-md-2">
    <input ng-disabled="true" ng-model="markup" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/>
  </div>
</div>
<?php $field_name = 'comissao';?>
<div class="form-group">
  <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Comissão(%) *</label>
  <div class="col-md-2">
    <input ng-disabled="true" ng-model="comissao" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/>
  </div>
</div>
<?php $field_name = 'comissao_indicacao';?>
<div class="form-group">
  <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Comissão Indicação(%) *</label>
  <div class="col-md-2">
    <input ng-disabled="true" ng-model="comissao_indicacao" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/>
  </div>
</div>

<?php $field_name = 'repasse_comissao';?>
<div class="form-group">
  <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Repasse comissão *</label>
  <div class="col-md-2">
    <input ng-disabled="true" ng-model="repasse_maximo" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/>
  </div>
</div>

<br>


<?php $field_name = 'padrao_comissao';?>
<div class="form-group">
  <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Comissão (Padrão) (%) *</label>
  <div class="col-md-2">
    <input ng-disabled="true" ng-model="padrao_comissao" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/>
  </div>
</div>

<?php $field_name = 'padrao_comissao_indicacao';?>
<div class="form-group">
  <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Comissão Indicação (Padrão) (%) *</label>
  <div class="col-md-2">
    <input ng-disabled="true" ng-model="padrao_comissao_indicacao" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/>
  </div>
</div>

<?php $field_name = 'padrao_repasse_comissao';?>
<div class="form-group">
  <label class="col-md-2 control-label" for="<?php echo $field_name;?>">Repasse comissão (Padrão) </label>
  <div class="col-md-2">
    <input ng-disabled="true" ng-model="padrao_repasse_maximo" ui-number-mask class="form-control" id="<?php echo $field_name ?>" name="<?php echo $field_name ?>" type="text"/>
  </div>
</div>
<?php else : ?>

<strong>Não Configurado</strong>
<?php endif;  ?>
