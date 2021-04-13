<!-- Tabs Heading -->
<div class="card-body">
    <ul class="nav nav-tabs">
        <li<?php if (is_current_controller(array('produtos_parceiros_configuracao', 'produtos_parceiros_configuracao_comissao', 'produtos_parceiros_regra_preco', 'produtos_parceiros_desconto', 'produtos_parceiros_cancelamento'))) : ?> class="active" <?php endif; ?>><a class="glyphicons list" href="<?php echo base_url("admin/produtos_parceiros_configuracao_comissao/edit/{$produto_parceiro_id}")?>"><i></i><span>Regras de negócios</span></a></li>
        <li<?php if (is_current_controller(array('produtos_parceiros_termo', 'produtos_parceiros_apolice', 'produtos_parceiros_apolice_range', 'produtos_parceiros_capitalizacao', 'produtos_parceiros_campos', 'produtos_parceiros_servico','produtos_parceiros_cliente_status'))) : ?> class="active" <?php endif; ?>><a class="glyphicons list" href="<?php echo base_url("admin/produtos_parceiros_termo/edit/{$produto_parceiro_id}")?>"><i></i><span>Produto</span></a></li>
        <li<?php if (is_current_controller(array('produtos_parceiros_pagamento', 'produtos_parceiros_configuracao_pagamento'))) : ?> class="active" <?php endif; ?>><a class="glyphicons list" href="<?php echo base_url("admin/produtos_parceiros_pagamento/index/{$produto_parceiro_id}")?>"><i></i><span>Financeiro</span></a></li>
        <li<?php if (is_current_controller(array('produtos_parceiros_comunicacao'))) : ?> class="active" <?php endif; ?>><a class="glyphicons list" href="<?php echo base_url("admin/produtos_parceiros_comunicacao/index/{$produto_parceiro_id}")?>"><i></i><span>Disparos</span></a></li>
        <li<?php if (is_current_controller(array('produtos_parceiros_resumo'))) : ?> class="active" <?php endif; ?>><a class="glyphicons list" href="<?php echo base_url("admin/produtos_parceiros_resumo/index/{$produto_parceiro_id}")?>"><i></i><span>Resumo</span></a></li>
    </ul>
</div>
<!--
<div class="card-body">
    <ul class="nav nav-tabs">
        <li<?php if (is_current_controller(array('produtos_parceiros_pagamento'))) : ?> class="active" <?php endif; ?>><a class="glyphicons cogwheel" href="<?php echo base_url("admin/produtos_parceiros_pagamento/index/{$produto_parceiro_id}")?>" ><i></i><span>Forma de Pagamento</span></a></li>
        <li<?php if (is_current_controller(array('produtos_parceiros_desconto'))) : ?> class="active" <?php endif; ?>><a class="glyphicons snowflake" href="<?php echo base_url("admin/produtos_parceiros_desconto/edit/{$produto_parceiro_id}")?>" ><i></i><span>Desconto</span></a></li>
        <li<?php if (is_current_controller(array('produtos_parceiros_comunicacao'))) : ?> class="active" <?php endif; ?>><a class="glyphicons snowflake" href="<?php echo base_url("admin/produtos_parceiros_comunicacao/index/{$produto_parceiro_id}")?>" ><i></i><span>Comunicação</span></a></li>
    </ul>
</div>
-->