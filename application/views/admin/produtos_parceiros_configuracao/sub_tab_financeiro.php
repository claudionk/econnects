<!-- Tabs Heading -->
<ul class="card-head nav nav-tabs">
    <li<?php if (is_current_controller(array('produtos_parceiros_pagamento'))) : ?> class="active" <?php endif; ?>><a class="glyphicons list" href="<?php echo base_url("admin/produtos_parceiros_pagamento/index/{$produto_parceiro_id}")?>"><i></i><span>Gateway Pagamento</span></a></li>
    <li<?php if (is_current_controller(array('produtos_parceiros_configuracao_pagamento'))) : ?> class="active" <?php endif; ?>><a class="glyphicons list" href="<?php echo base_url("admin/produtos_parceiros_configuracao_pagamento/edit/{$produto_parceiro_id}")?>"><i></i><span>Tipo de cobrança</span></a></li>
</ul>

