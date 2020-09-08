<?php $this->load->view('admin/pedido/front/step', array('step' => 5, 'produto_parceiro_id' => $produto_parceiro_id));

if($_POST)
    $row = $_POST;
?>

<div class="step-six">
    <h3 class="text-ultra-bold text-success">SEU PEDIDO FOI CANCELADO COM SUCESSO</h3>
    <p class="text-sm-left">Você receberá no e-mail <b><?= $email; ?></b> o Termo de Solicitação de Desistência do Seguro.</p>
</div>

<?php
$this->load->view('admin/venda/equipamento/components/btn-info');
$this->load->view('admin/venda/equipamento/components/btn-whatsapp');
$this->load->view('admin/venda/equipamento/components/footer');
?>