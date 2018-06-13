<!-- Tabs Heading -->
<ul class="card-head nav nav-tabs">
    <li<?php if (is_current_controller(array('produtos_parceiros_termo'))) : ?> class="active" <?php endif; ?>><a class="glyphicons list" href="<?php echo base_url("admin/produtos_parceiros_termo/edit/{$produto_parceiro_id}")?>"><i></i><span>Termo</span></a></li>
    <li<?php if (is_current_controller(array('produtos_parceiros_apolice'))) : ?> class="active" <?php endif; ?>><a class="glyphicons list" href="<?php echo base_url("admin/produtos_parceiros_apolice/edit/{$produto_parceiro_id}")?>"><i></i><span>Certificado / Bilhete</span></a></li>
    <li<?php if (is_current_controller(array('produtos_parceiros_apolice_range'))) : ?> class="active" <?php endif; ?>><a class="glyphicons list" href="<?php echo base_url("admin/produtos_parceiros_apolice_range/index/{$produto_parceiro_id}")?>"><i></i><span>Apolice Range</span></a></li>
    <li<?php if (is_current_controller(array('produtos_parceiros_capitalizacao'))) : ?> class="active" <?php endif; ?>><a class="glyphicons list" href="<?php echo base_url("admin/produtos_parceiros_capitalizacao/index/{$produto_parceiro_id}")?>"><i></i><span>Capitalização</span></a></li>
    <li<?php if (is_current_controller(array('produtos_parceiros_campos'))) : ?> class="active" <?php endif; ?>><a class="glyphicons list" href="<?php echo base_url("admin/produtos_parceiros_campos/index/{$produto_parceiro_id}")?>"><i></i><span>Campos</span></a></li>
    <li<?php if (is_current_controller(array('produtos_parceiros_servico'))) : ?> class="active" <?php endif; ?>><a class="glyphicons list" href="<?php echo base_url("admin/produtos_parceiros_servico/index/{$produto_parceiro_id}")?>"><i></i><span>Serviços</span></a></li>
    <li<?php if (is_current_controller(array('produtos_parceiros_traducao'))) : ?> class="active" <?php endif; ?>><a class="glyphicons list" href="<?php echo base_url("admin/produtos_parceiros_traducao/index/{$produto_parceiro_id}")?>"><i></i><span>Tradução</span></a></li>
</ul>

