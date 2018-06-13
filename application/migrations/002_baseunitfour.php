<?php
/**
 * Created by PhpStorm.
 * User: danil
 * Date: 29/08/2016
 * Time: 10:32
 */
class Migration_Baseunitfour extends CI_Migration {

    public function up() {

            $this->load->dbforge(); // DB Forge, para manipular o banco

            $this->db->query("SET FOREIGN_KEY_CHECKS=0;");
            $sql = "
                        CREATE TABLE IF NOT EXISTS `base_pessoa` (
                          `base_pessoa_id` INT(11) NOT NULL AUTO_INCREMENT,
                          `cliente_id` INT(11) NOT NULL DEFAULT 0,
                          `documento` VARCHAR(20) NULL DEFAULT NULL,
                          `nome` VARCHAR(200) NULL DEFAULT NULL,
                          `sobrenome` VARCHAR(50) NULL DEFAULT NULL,
                          `sexo` ENUM('M', 'F') NULL DEFAULT NULL,
                          `nome_mae` VARCHAR(200) NULL DEFAULT NULL,
                          `data_nascimento` DATE NULL DEFAULT NULL,
                          `signo` VARCHAR(50) NULL DEFAULT NULL,
                          `situacao_receita` VARCHAR(100) NULL DEFAULT NULL,
                          `ultima_atualizacao` TIMESTAMP NULL DEFAULT NULL,
                          `quantidade_atualziacao` INT(11) NULL DEFAULT 0,
                          `deletado` TINYINT(4) NULL DEFAULT 0,
                          `criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                          `alteracao_usuario_id` INT(11) NULL DEFAULT 0,
                          `alteracao` TIMESTAMP NULL DEFAULT NULL,
                          PRIMARY KEY (`base_pessoa_id`))
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8;
                        
             ";
            $this->db->query($sql);
            $sql = "
                       CREATE TABLE IF NOT EXISTS `base_pessoa_contato` (
                              `base_pessoa_contato_id` INT(11) NOT NULL AUTO_INCREMENT,
                              `base_pessoa_id` INT(11) NOT NULL,
                              `contato_tipo_id` INT(11) NOT NULL,
                              `ranking` INT(11) NULL DEFAULT NULL,
                              `nome` VARCHAR(50) NULL DEFAULT NULL,
                              `contato` VARCHAR(255) NULL DEFAULT NULL,
                              `deletado` TINYINT(4) NULL DEFAULT 0,
                              `criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                              `alteracao_usuario_id` INT(11) NULL DEFAULT 0,
                              `alteracao` TIMESTAMP NULL DEFAULT NULL,
                              PRIMARY KEY (`base_pessoa_contato_id`),
                              INDEX `fk_base_pessoa_contato_contato_tipo1_idx` (`contato_tipo_id` ASC),
                              INDEX `fk_base_pessoa_contato_base_pessoa1_idx` (`base_pessoa_id` ASC),
                              CONSTRAINT `fk_base_pessoa_contato_contato_tipo1`
                                FOREIGN KEY (`contato_tipo_id`)
                                REFERENCES `contato_tipo` (`contato_tipo_id`)
                                ON DELETE NO ACTION
                                ON UPDATE NO ACTION,
                              CONSTRAINT `fk_base_pessoa_contato_base_pessoa1`
                                FOREIGN KEY (`base_pessoa_id`)
                                REFERENCES `base_pessoa` (`base_pessoa_id`)
                                ON DELETE NO ACTION
                                ON UPDATE NO ACTION)
                            ENGINE = InnoDB
                            DEFAULT CHARACTER SET = utf8;
              ";
              $this->db->query($sql);
              $sql = "  
                        CREATE TABLE IF NOT EXISTS `base_pessoa_endereco` (
                          `base_pessoa_endereco_id` INT(11) NOT NULL AUTO_INCREMENT,
                          `base_pessoa_id` INT(11) NOT NULL,
                          `ranking` INT(11) NULL DEFAULT NULL,
                          `endereco_cep` VARCHAR(10) NULL DEFAULT NULL,
                          `endereco` VARCHAR(200) NULL DEFAULT NULL,
                          `endereco_numero` VARCHAR(20) NULL DEFAULT NULL,
                          `endereco_complemento` VARCHAR(50) NULL DEFAULT NULL,
                          `endereco_bairro` VARCHAR(200) NULL DEFAULT NULL,
                          `endereco_cidade` VARCHAR(200) NULL DEFAULT NULL,
                          `endereco_uf` VARCHAR(2) NULL DEFAULT NULL,
                          `deletado` TINYINT(4) NULL DEFAULT 0,
                          `criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                          `alteracao_usuario_id` INT(11) NULL DEFAULT 0,
                          `alteracao` TIMESTAMP NULL DEFAULT NULL,
                          INDEX `fk_base_pessoa_endereco_base_pessoa1_idx` (`base_pessoa_id` ASC),
                          PRIMARY KEY (`base_pessoa_endereco_id`),
                          CONSTRAINT `fk_base_pessoa_endereco_base_pessoa1`
                            FOREIGN KEY (`base_pessoa_id`)
                            REFERENCES `base_pessoa` (`base_pessoa_id`)
                            ON DELETE NO ACTION
                            ON UPDATE NO ACTION)
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8;
               ";
                $this->db->query($sql);
                $sql = "
                    CREATE TABLE IF NOT EXISTS `base_pessoa_empresa` (
                      `base_pessoa_empresa_id` INT(11) NOT NULL AUTO_INCREMENT,
                      `base_pessoa_id` INT(11) NOT NULL,
                      `ranking` INT(11) NULL DEFAULT NULL,
                      `nome` VARCHAR(200) NULL DEFAULT NULL,
                      `documento` VARCHAR(20) NULL DEFAULT NULL,
                      `participacao` DECIMAL(15,2) NULL DEFAULT NULL,
                      `data_entrada` DATE NULL DEFAULT NULL,
                      `deletado` TINYINT(4) NULL DEFAULT 0,
                      `criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                      `alteracao_usuario_id` INT(11) NULL DEFAULT 0,
                      `alteracao` TIMESTAMP NULL DEFAULT NULL,
                      PRIMARY KEY (`base_pessoa_empresa_id`),
                      INDEX `fk_base_pessoa_empresa_base_pessoa1_idx` (`base_pessoa_id` ASC),
                      CONSTRAINT `fk_base_pessoa_empresa_base_pessoa1`
                        FOREIGN KEY (`base_pessoa_id`)
                        REFERENCES `base_pessoa` (`base_pessoa_id`)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION)
                    ENGINE = InnoDB
                    DEFAULT CHARACTER SET = utf8;                        
                ";

            $this->db->query($sql);
                $sql = "
                        CREATE TABLE IF NOT EXISTS `servico_tipo` (
                          `servico_tipo_id` INT(11) NOT NULL AUTO_INCREMENT,
                          `slug` VARCHAR(50) NULL DEFAULT NULL,
                          `nome` VARCHAR(100) NULL DEFAULT NULL,
                          `deletado` TINYINT(4) NULL DEFAULT 0,
                          `criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                          `alteracao_usuario_id` INT(11) NULL DEFAULT 0,
                          `alteracao` TIMESTAMP NULL DEFAULT NULL,
                          PRIMARY KEY (`servico_tipo_id`))
                        ENGINE = InnoDB
                        DEFAULT CHARACTER SET = utf8;                        
                ";

            $this->db->query($sql);
                $sql = "
                    CREATE TABLE IF NOT EXISTS `servico` (
                      `servico_id` INT(11) NOT NULL AUTO_INCREMENT,
                      `servico_tipo_id` INT(11) NOT NULL,
                      `nome` VARCHAR(100) NULL DEFAULT NULL,
                      `descricao` VARCHAR(255) NULL DEFAULT NULL,
                      `usuario` VARCHAR(255) NULL DEFAULT NULL,
                      `senha` VARCHAR(255) NULL DEFAULT NULL,
                      `parametros` VARCHAR(255) NULL DEFAULT NULL,
                      `token` VARCHAR(255) NULL DEFAULT NULL,
                      `token_validade` TIMESTAMP NULL DEFAULT NULL,
                      `deletado` TINYINT(4) NULL DEFAULT 0,
                      `criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                      `alteracao_usuario_id` INT(11) NULL DEFAULT 0,
                      `alteracao` TIMESTAMP NULL DEFAULT NULL,
                      PRIMARY KEY (`servico_id`),
                      INDEX `fk_servico_servico_tipo1_idx` (`servico_tipo_id` ASC),
                      CONSTRAINT `fk_servico_servico_tipo1`
                        FOREIGN KEY (`servico_tipo_id`)
                        REFERENCES `servico_tipo` (`servico_tipo_id`)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION)
                    ENGINE = InnoDB
                    DEFAULT CHARACTER SET = utf8;                        
                ";

            $this->db->query($sql);
                $sql = "
                            CREATE TABLE IF NOT EXISTS `produto_parceiro_servico` (
                              `produto_parceiro_servico_id` INT(11) NOT NULL AUTO_INCREMENT,
                              `produto_parceiro_id` INT(11) NOT NULL,
                              `servico_id` INT(11) NOT NULL,
                              `deletado` TINYINT(4) NULL DEFAULT 0,
                              `criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                              `alteracao_usuario_id` INT(11) NULL DEFAULT 0,
                              `alteracao` TIMESTAMP NULL DEFAULT NULL,
                              PRIMARY KEY (`produto_parceiro_servico_id`),
                              INDEX `fk_produto_parceiro_servico_produto_parceiro1_idx` (`produto_parceiro_id` ASC),
                              INDEX `fk_produto_parceiro_servico_servico1_idx` (`servico_id` ASC),
                              CONSTRAINT `fk_produto_parceiro_servico_produto_parceiro1`
                                FOREIGN KEY (`produto_parceiro_id`)
                                REFERENCES `produto_parceiro` (`produto_parceiro_id`)
                                ON DELETE NO ACTION
                                ON UPDATE NO ACTION,
                              CONSTRAINT `fk_produto_parceiro_servico_servico1`
                                FOREIGN KEY (`servico_id`)
                                REFERENCES `servico` (`servico_id`)
                                ON DELETE NO ACTION
                                ON UPDATE NO ACTION)
                            ENGINE = InnoDB
                            DEFAULT CHARACTER SET = utf8;                        
                ";

            $this->db->query($sql);
                $sql = "
 
                CREATE TABLE IF NOT EXISTS `produto_parceiro_servico_log` (
                  `produto_parceiro_servico_log_id` INT(11) NOT NULL AUTO_INCREMENT,
                  `produto_parceiro_servico_id` INT(11) NOT NULL,
                  `url` VARCHAR(255) NULL DEFAULT NULL,
                  `consulta` VARCHAR(20) NULL DEFAULT NULL,
                  `parametros` TEXT NULL DEFAULT NULL,
                  `retorno` LONGTEXT NULL DEFAULT NULL,
                  `time_envio` TIME NULL DEFAULT NULL,
                  `time_retorno` TIME NULL DEFAULT NULL,
                  `data_log` TIMESTAMP NULL DEFAULT NULL,
                  `ip` VARCHAR(15) NULL DEFAULT NULL,
                  `deletado` TINYINT(4) NULL DEFAULT 0,
                  `criacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                  `alteracao_usuario_id` INT(11) NULL DEFAULT 0,
                  `alteracao` TIMESTAMP NULL DEFAULT NULL,
                  PRIMARY KEY (`produto_parceiro_servico_log_id`),
                  INDEX `fk_produto_parceiro_servico_log_produto_parceiro_servico1_idx` (`produto_parceiro_servico_id` ASC),
                  CONSTRAINT `fk_produto_parceiro_servico_log_produto_parceiro_servico1`
                    FOREIGN KEY (`produto_parceiro_servico_id`)
                    REFERENCES `produto_parceiro_servico` (`produto_parceiro_servico_id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION)
                ENGINE = InnoDB
                DEFAULT CHARACTER SET = utf8;
                  
                ";

            $this->db->query($sql);

            $sql = "INSERT INTO `servico_tipo` (`servico_tipo_id`, `slug`, `nome`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES (1, 'unitfour_pf', 'Busca de Dados PF', 0, '2016-08-31 17:17:00', 0, NULL);";
            $this->db->query($sql);
            $this->db->query("SET FOREIGN_KEY_CHECKS=1;");

    }

    public function down() {

        $this->db->trans_start();
        $this->db->query("SET FOREIGN_KEY_CHECKS=0;");
        $this->db->query("DROP TABLE IF EXISTS `base_pessoa`;");
        $this->db->query("DROP TABLE IF EXISTS `base_pessoa_contato`;");
        $this->db->query("DROP TABLE IF EXISTS `base_pessoa_endereco`;");
        $this->db->query("DROP TABLE IF EXISTS `base_pessoa_empresa`;");
        $this->db->query("DROP TABLE IF EXISTS `servico_tipo`;");
        $this->db->query("DROP TABLE IF EXISTS `servico`;");
        $this->db->query("DROP TABLE IF EXISTS `produto_parceiro_servico`;");
        $this->db->query("DROP TABLE IF EXISTS `produto_parceiro_servico_log`;");
        $this->db->query("SET FOREIGN_KEY_CHECKS=1;");
        $this->db->trans_complete();


    }
}

