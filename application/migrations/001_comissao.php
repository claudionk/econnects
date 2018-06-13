<?php
/**
 * Created by PhpStorm.
 * User: danil
 * Date: 29/08/2016
 * Time: 10:32
 */
class Migration_Comissao extends CI_Migration {

    public function up() {
        if (! $this->db->table_exists('comissao_classe')) {

            $sql = "
                CREATE TABLE IF NOT EXISTS `comissao_classe` (
                  `comissao_classe_id` INT(11) NOT NULL AUTO_INCREMENT,
                  `nome` VARCHAR(50) NULL DEFAULT NULL,
                  `slug` VARCHAR(50) NULL DEFAULT NULL,
                  `deletado` TINYINT(4) NULL DEFAULT 0,
                  `criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                  `alteracao_usuario_id` INT(11) NULL DEFAULT 0,
                  `alteracao` TIMESTAMP NULL DEFAULT NULL,
                  PRIMARY KEY (`comissao_classe_id`))
                ENGINE = InnoDB
                DEFAULT CHARACTER SET = utf8;         
                ";
            $this->db->query($sql);

            $sql = "
                INSERT INTO `comissao_classe` (`comissao_classe_id`, `nome`, `slug`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (1, 'PARCEIRO', 'parceiro', 0, '2016-08-29 11:54:24', 0, '2016-08-29 11:54:24');
            ";
            $this->db->query($sql);
            $sql = "
                INSERT INTO `comissao_classe` (`comissao_classe_id`, `nome`, `slug`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (2, 'PARCEIRO USUÁRIO', 'parceiro_usuario', 0, '2016-08-29 11:54:24', 0, '2016-08-29 11:54:24');
            ";
            $this->db->query($sql);
            $sql = "
                INSERT INTO `comissao_classe` (`comissao_classe_id`, `nome`, `slug`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (3, 'PARCEIRO USUARIO INDICAÇÃO', 'parceiro_usuario_indicacao', 0, '2016-08-29 11:54:24', 0, '2016-08-29 11:54:24');
            ";
            $this->db->query($sql);
            $sql = "
                INSERT INTO `comissao_classe` (`comissao_classe_id`, `nome`, `slug`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (4, 'PARCEIRO RELACIONAMENTO', 'parceiro_relacionamento', 0, '2016-08-30 14:47:56', 0, NULL);
            ";
            $this->db->query($sql);

        }


        if (! $this->db->table_exists('comissao_gerada')) {

            $sql = "
                                           
                    CREATE TABLE IF NOT EXISTS `comissao_gerada` (
                      `comissao_gerada_id` INT(11) NOT NULL AUTO_INCREMENT,
                      `comissao_classe_id` INT(11) NOT NULL,
                      `pedido_id` INT(11) NOT NULL DEFAULT 0,
                      `parceiro_id` INT(11) NOT NULL DEFAULT 0,
                      `usuario_id` INT(11) NOT NULL DEFAULT 0,
                      `comissao` DECIMAL(15,3) NULL DEFAULT NULL,
                      `premio_liquido_total` DECIMAL(15,3) NULL DEFAULT NULL,
                      `valor` DECIMAL(15,3) NULL DEFAULT NULL,
                      `descricao` VARCHAR(255) NULL DEFAULT NULL,
                      `observacao` VARCHAR(255) NULL DEFAULT NULL,
                      `faturado` TINYINT(4) NULL DEFAULT NULL,
                      `deletado` TINYINT(4) NULL DEFAULT 0,
                      `criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                      `alteracao_usuario_id` INT(11) NULL DEFAULT 0,
                      `alteracao` TIMESTAMP NULL DEFAULT NULL,
                      PRIMARY KEY (`comissao_gerada_id`),
                      INDEX `fk_comissao_gerada_comissao_classe1_idx` (`comissao_classe_id` ASC),
                      CONSTRAINT `fk_comissao_gerada_comissao_classe1`
                        FOREIGN KEY (`comissao_classe_id`)
                        REFERENCES `comissao_classe` (`comissao_classe_id`)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION)
                    ENGINE = InnoDB
                    DEFAULT CHARACTER SET = utf8                                           
                                           
                ";
            $this->db->query($sql);
        }


        $sql = "SELECT * FROM usuario_acl_recurso WHERE slug='comissao_classe' AND deletado = 0";
        $result =  $this->db->query($sql);

        if($result->num_rows() == 0){
            $sql = "INSERT INTO `usuario_acl_recurso` (`pai_id`, `ordem`, `nome`, `slug`, `controller`, `acao`, `icon`, `criacao`, `alteracao`) VALUES ('83', '10', 'Classe de Comissões', 'comissao_classe', 'comissao_classe', 'index', '', '2016-04-28 04:02:35', '2016-06-21 05:38:40')";
            $this->db->query($sql);
        }
            $sql = "INSERT INTO `usuario_acl_recurso` (`pai_id`, `ordem`, `nome`, `slug`, `controller`, `acao`, `parametros`, `url`, `externo`, `target`, `icon`, `exibir_menu`, `criacao`, `alteracao_usuario_id`, `alteracao`, `deletado`) VALUES (5, 5, 'Comissões', 'comissao_gerada', 'comissao_gerada', 'index', '', '', 0, '_self', NULL, 1, '2016-06-21 03:18:51', 0, '2016-06-21 06:34:41', 0);";
            $this->db->query($sql);



    }

    public function down() {
        $this->load->dbforge(); // DB Forge, para manipular o banco
        if ($this->db->table_exists('comissao_gerada')) {

            $this->dbforge->drop_table('comissao_gerada',TRUE);

        }

        if ($this->db->table_exists('comissao_classe')) {
            $this->db->trans_start();
            $this->db->query("SET FOREIGN_KEY_CHECKS=0;");
            $this->db->query("DROP TABLE `comissao_classe`;");
            $this->db->query("SET FOREIGN_KEY_CHECKS=1;");
            $this->db->trans_complete();
        }

        $sql = "SELECT * FROM usuario_acl_recurso WHERE slug='comissao_classe' AND deletado = 0";
        $result =  $this->db->query($sql);

        if($result->num_rows() > 0){
            $sql = "DELETE FROM usuario_acl_recurso WHERE slug='comissao_classe'";
            $this->db->query($sql);
        }

    }
}

