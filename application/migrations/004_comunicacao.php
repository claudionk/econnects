<?php
/**
 * Created by PhpStorm.
 * User: danil
 * Date: 29/08/2016
 * Time: 10:32
 */
class Migration_Comunicacao extends CI_Migration {

    public function up() {

            $this->load->dbforge(); // DB Forge, para manipular o banco

            $this->db->query("SET FOREIGN_KEY_CHECKS=0;");
            $sql =
            "
                INSERT INTO `comunicacao_evento` (`comunicacao_tipo_id`, `nome`, `slug`, `descricao`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (1, 'COTAÇÃO SALVA', 'cotacao_salva', 'ESTE EVENTO OCORRE QUANDO UMA COTAÇÃO É SALVA.', 0, '2016-09-23 14:53:08', 0, '2016-09-23 14:53:08');
                INSERT INTO `comunicacao_template` (`comunicacao_tipo_id`, `comunicacao_engine_configuracao_id`, `descricao`, `slug`, `mensagem_titulo`, `mensagem_de`, `mensagem_anexo`, `mensagem`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (1, 1, 'COTAÇÃO SALVA', 'cotacao_salva', 'Cotação salva com sucesso', 'econnects@zazz.com.br', NULL, '<p>Caro Sr(a). {nome},<br />\n<br />\nA cota&ccedil;&atilde;o foi gerada com sucesso,&nbsp;<br />\n<br />\n<strong>CPF</strong>: {cpf}<br />\n<strong>Nome</strong>: {nome}<br />\n<strong>Celular</strong>: {telefone}<br />\n<strong>E-mail</strong>: {email}<br />\n<strong>Data de nascimento</strong>: {data_nascimento}</p>\n\n<p><strong>Nome do plano</strong>: {plano_nome}<br />\n<strong>N&uacute;mero de passageiros:</strong> {num_passageiro}</p>\n\n<p><strong>Data de sa&iacute;da:</strong> {data_saida}<br />\n<strong>Data de retorno</strong>: {data_retorno}</p>\n\n<p><strong>Valor total</strong>: R${valor_total}</p>\n\n<p><br />\nObrigado,<br />\nE-connects agradece.</p>\n', 0, '2016-09-23 14:49:52', 0, '2016-09-23 15:41:56');
                UPDATE forma_pagamento_integracao SET producao = 1 where forma_pagamento_integracao_id = 1;
                UPDATE usuario_acl_recurso SET ordem = 4 where usuario_acl_recurso_id = 126;
                UPDATE usuario_acl_recurso SET deletado = 1 where usuario_acl_recurso_id = 128;
             ";
            $this->db->query($sql);

    }

    public function down() {

        $this->db->trans_start();
        $this->db->query("SET FOREIGN_KEY_CHECKS=0;");
        $this->db->query("DELETE FROM `comunicacao_evento` WHERE comunicacao_evento_id = 6");
        $this->db->query("DELETE FROM `comunicacao_template` WHERE comunicacao_template = 6");
        $this->db->query("UPDATE forma_pagamento_integracao SET producao = 0 where forma_pagamento_integracao_id = 1");
        $this->db->query("UPDATE usuario_acl_recurso SET ordem = 8 where usuario_acl_recurso_id = 126");
        $this->db->query("UPDATE usuario_acl_recurso SET deletado = 0 where usuario_acl_recurso_id = 128");

        $this->db->query("SET FOREIGN_KEY_CHECKS=1;");
        $this->db->trans_complete();


    }
}

