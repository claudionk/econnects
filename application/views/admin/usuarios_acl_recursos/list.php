<div class="layout-app">
    <!-- row -->
    <div class="row row-app">
        <!-- col -->
        <div class="col-md-12">

            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active"><?php echo app_recurso_nome();?></li>
                </ol>

            </div>

            <!-- col-separator -->
            <div class="col-separator col-separator-first col-unscrollable">
                <div class="card">

                    <!-- Widget heading -->
                    <div class="card-body">
                        <div class="col-md-3">
                            <a href="<?php echo base_url("$current_controller_uri/add")?>" class="btn btn-app btn-primary">
                                <i class="fa  fa-plus"></i> Adicionar
                            </a>
                            <a href="javascript: void(0);" class="btn salvar-ordem btn-app btn-primary">
                                <i class="fa  fa-sort-amount-asc"></i> Salvar Ordem
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card">

                    <!-- Widget heading -->
                    <div class="card-body">
                        <?php $field_name = 'pai_id';?>
                        <form class="form-horizontal margin-none" id="validateSubmitForm" method="post" autocomplete="off">
                            <input type="hidden" name="url_ordem" id="url_ordem" value="<?php echo base_url("$current_controller_uri/set_ordem")?>">
                            <label class="col-md-2 control-label" for="<?php echo $field_name;?>">menu Principal (Pai) *</label>
                            <div class="col-md-6">
                                <select class="form-control" name="<?php echo $field_name;?>" id="<?php echo $field_name;?>">
                                    <?php foreach ($list_pai as $index => $item) : ?>
                                        <option  value="<?php echo $index; ?>"
                                            <?php if(isset($pai_id)){if($pai_id == $index) {echo " selected ";};}; ?> >
                                            <?php echo $item; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>
                 </div>

                <div class="row">
                    <div class="col-md-6">
                        <?php $this->load->view('admin/partials/messages'); ?>
                    </div>
                </div>



                <!-- Widget -->
                <div class="card">


                    <div class="card-body">


                        <!-- Table -->
                        <table class="table table-hover" id="tabela-ordem">

                            <!-- Table heading -->
                            <thead>
                            <tr>
                                <th width="10%" class="center">ID</th>
                                <th width="10%" class="center">#Ordem</th>
                                <th width="30%">Nome</th>
                                <th width="20%">Slug</th>
                                <th class="center" width="30%">Ações</th>
                            </tr>
                            </thead>
                            <!-- // Table heading END -->

                            <!-- Table body -->
                            <tbody>

                            <!-- Table row -->
                            <?php $i =0; ?>
                            <?php foreach($rows as $row) :?>
                            <tr data-id="<?php echo $row[$primary_key];?>" data-ordem="<?php echo $i; ?>">

                                <td class="center"><?php echo $row[$primary_key];?></td>
                                <td><?php echo $row['ordem'];?></td>
                                <td><?php echo $row['nome'];?></td>
                                <td><?php echo $row['slug'];?></td>
                                <td class="center">
                                    <a href="<?php echo base_url("{$current_controller_uri}/edit/{$row[$primary_key]}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-edit"></i>  Editar </a>
                                    <a href="<?php echo base_url("admin/usuarios_acl_recursos_acoes/edit/{$row[$primary_key]}")?>" class="btn btn-sm btn-primary">  <i class="fa fa-edit"></i>  Ações </a>

                                    <a href="<?php echo base_url("$current_controller_uri/delete/{$row[$primary_key]}")?>" class="btn btn-sm btn-danger deleteRowButton"> <i class="fa fa-eraser"></i> Excluir </a>
                                </td>
                            </tr>
                            <?php $i++; ?>
                            <?php endforeach;?>
                            <!-- // Table row END -->

                            </tbody>
                            <!-- // Table body END -->

                        </table>
                        <!-- // Table END -->
                    </div>
                </div>
                <!-- // Widget END -->
            </div>
        </div>
    </div>
</div>