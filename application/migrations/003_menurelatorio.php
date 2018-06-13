<?php
/**
 * Created by PhpStorm.
 * User: danil
 * Date: 29/08/2016
 * Time: 10:32
 */
class Migration_Menurelatorio extends CI_Migration {

    public function up() {

            $this->load->dbforge(); // DB Forge, para manipular o banco

            $this->db->query("SET FOREIGN_KEY_CHECKS=0;");
            $sql ="INSERT INTO `usuario_acl_recurso` (`pai_id`, `ordem`, `nome`, `slug`, `controller`, `acao`, `parametros`, `url`, `externo`, `target`, `icon`, `exibir_menu`, `criacao`, `alteracao_usuario_id`, `alteracao`, `deletado`) VALUES (0, 8, 'Relatórios', 'relatorios', 'relatorios', 'index', '', '', 0, '_self', 'fa fa-bar-chart', 1, '2016-04-28 06:18:59', 0, '2016-04-28 07:17:15', 0);";
            $this->db->query($sql);
            $sql ="INSERT INTO `usuario_acl_recurso` (`pai_id`, `ordem`, `nome`, `slug`, `controller`, `acao`, `parametros`, `url`, `externo`, `target`, `icon`, `exibir_menu`, `criacao`, `alteracao_usuario_id`, `alteracao`, `deletado`) VALUES (126, 8, 'Vendas', 'relatorios_vendas', 'relatorios', 'index', '', '', 0, '_self', '', 1, '2016-04-28 06:18:59', 0, '2016-04-28 07:17:15', 0);";
            $this->db->query($sql);

    }

    public function down() {

        $this->db->trans_start();
        $this->db->query("SET FOREIGN_KEY_CHECKS=0;");
        $this->db->query("DELETE FROM `usuario_acl_recurso` WHERE slug = 'relatorios'");
        $this->db->query("DELETE FROM `usuario_acl_recurso` WHERE slug = 'relatorios_vendas'");
        $this->db->query("SET FOREIGN_KEY_CHECKS=1;");
        $this->db->trans_complete();


    }
}

