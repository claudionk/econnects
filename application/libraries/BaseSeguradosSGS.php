<?php

class BaseSeguradosSGS {

    private $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }
    public function MatchEquipamento($modelo, $marca = null, $equipamento = null, $ean = null)
    {
        $indiceMax = 60;
        $id_marca = $id_equipamento_prod = $id_linha = "9999";
        $id_produto_dicionario = null;
        $findModelo = true;

        if (!empty($ean)) {
            $match = $this->matchProdutosEAN($ean);
            if (!empty($match)) {
                $id_marca = $match->id_marca_sgs;
                $id_equipamento_prod = $match->id_produto;
                $id_linha = $match->id_linha;
                $findModelo = false;
            }
        }

        if ($findModelo && !empty($modelo)) {

            $modelExp = explode(" ", $modelo);
            $aReturn = [];

            foreach($modelExp as $k => $v) {
                if(!empty($v)) {
                    $aReturn[] = "'" . str_replace('*', '', str_replace(',', '', str_replace('\'', '\\\'', $v))) . "'";
                }
            }

            $match = $this->matchProdutosDicionario( implode(",", $aReturn) );
            if (!empty($match)) {
                $id_marca = $match->id_marca_sgs;
                $id_equipamento_prod = $match->id_produto;
                $id_linha = $match->id_linha;
                $id_produto_dicionario = $match->id_produto_dicionario;

            // match apenas se houver um mínimo de palavras
            } elseif (count($aReturn) > 3) {
                $match = $this->matchProdutos($modelo ." ". $marca, true, $marca);
                if(!empty($match)){
                    //se o indice e maior do que o minimo estipulado de 10%
                    if($match->indice / $indiceMax > 0.1){
                        $id_marca = $match->id_marca_sgs;
                        $id_equipamento_prod = $match->id_produto;
                        $id_linha = $match->id_linha;
                    }
                }
            }

        }

        //se nao encontrou o produto no match
        if ($id_marca == "9999") $id_marca = $this->load_check_marca($marca,$modelo,$equipamento); //busca id_marca atraves da marca, modelo e equipamento do registro
        if ($id_equipamento_prod == "9999") $id_equipamento_prod = $this->load_check_produto($marca,$modelo,$equipamento); //busca id_produto atraves da marca, modelo e equipamento do registro
        if ($id_equipamento_prod == "9999" || $id_linha == "9999") $id_linha = $this->buscaIdLinha($id_equipamento_prod);

        // gera log de consulta
        $this->insereLogDicionario($modelo, $id_produto_dicionario, $id_equipamento_prod, $id_marca, $id_linha);

        return (object) [
            'id_marca' => $id_marca,
            'id_equipamento' => $id_equipamento_prod,
            'id_linha' => $id_linha,
            'id_produto_dicionario' => $id_produto_dicionario,
        ];
    }

    public function insereLogDicionario($palavra, $id_produto_dicionario = null, $id_produto = '9999', $id_marca = '9999', $id_linha = '9999')
    {
        $id_produto_dicionario = !empty($id_produto_dicionario) ? $id_produto_dicionario : 'NULL';
        $query = $this->CI->db->query("INSERT INTO sissolucoes1.sis_produto_dicionario_log (
                    palavra,
                    id_produto_dicionario,
                    id_produto,
                    id_marca,
                    id_linha
                )values(
                    '{$palavra}',
                    {$id_produto_dicionario},
                    {$id_produto},
                    {$id_marca},
                    {$id_linha}
                );");
        $last_id = $this->CI->db->insert_id();
        return $last_id; 
    }

    public function load_check_marca($marca,$modelo,$produto){
        $id_marca = "";

        //Check de Marca na tabela de marcas
        $res = $this->buscaMarca($marca);
        if($res){
            $id_marca = $res->id_marca;

            if ( $id_marca != "" )
                return $id_marca;
        }

        //Tenta buscar pela tabela "de para"
        $res = $this->buscaMarcaDePara($marca);
        if($res){
            $id_marca_depara = $res->id_marca;
            $marca_para = $res->marca_para;

            if ( $id_marca_depara != "" ){
                $res = $this->buscaMarca($marca_para);
                if($res){
                    $id_marca = $res->id_marca;
                    $marca = $res->marca;
                }
            }

            if ( $id_marca != "" )
                return $id_marca;
        }

        $MARCA = strtoupper($marca);
        $MODELO = strtoupper($modelo);
        $PRODUTO = strtoupper($produto);


        //tenta buscar marca no campo produto
        $res = $this->todasMarcas();
        if($res){
            foreach ($res as $row){
                $space = ( strlen($row["marca"]) < 4 ) ? ' ': '';
                $pos_modelo = strpos($space . $MODELO, $space . $row["marca"]);
                $pos_produto = strpos($space . $PRODUTO, $space . $row["marca"]);

                if (!$pos_modelo === false || !$pos_produto === false){
                    $id_marca = $row["id_marca"] ; $marca = $row["marca"] ;
                    return $id_marca; 
                }
            }
        }

         //tenta buscar marca por depara no campo descricao
        $res = $this->todasMarcasDePara();
        if($res){
            foreach ($res as $row) {
                $space = ( strlen($row["marca_de"]) < 4 ) ? ' ': '';
                $pos_modelo = strpos($space . $MODELO, $space . $row["marca_de"]);
                $pos_produto = strpos($space . $PRODUTO, $space . $row["marca_de"]);

                if (!$pos_modelo === false || !$pos_produto === false){
                    $marca_para = $row["marca_para"] ;

                    $res2 = $this->buscaMarca($marca_para);
                    if($res2){
                        $id_marca = $res->id_marca;
                        $marca = $res->marca;

                        return $id_marca;
                    }
                }
            }
        }

        if ($id_marca==""){
            $id_marca = "9999";
            return $id_marca;
        }
    }

    public function todasMarcasDePara()
    {
        $retorno = null;
        return $this->getArrayCollection("select marca_de, marca_para , length(marca_de) tamanho from sissolucoes1.sis_marcas_depara order by tamanho desc");
    }

    public function buscaMarca($marca)
    {
        $retorno = null;
        $query = $this->getArrayCollection("select id_marca, marca from sissolucoes1.sis_marcas where upper(marca) = upper('{$marca}')");
        if(!empty($query))
            $retorno = (object) $query[0];

        return $retorno;
    }

    public function matchProdutosEAN($ean)
    {
        $retorno = null;
        $sql = "SELECT beel.ean, beel.name, beMSis.id_marca_sgs AS id_marca_sgs, p.id_produto, pl.id_linha
        FROM business_engine.Equipamentos beel
        JOIN business_engine.Equipamentos_Marcas beM ON beel.idMarca = beM.idEquipamentos_Marcas
        JOIN business_engine.Equipamentos_Marcas_SIS beMSis ON beM.idEquipamentos_Marcas = beMSis.idMarca
        JOIN business_engine.Equipamentos_Linhas beL ON (beel.category = beL.idEquipamentos_Linhas OR beel.subCategory = beL.idEquipamentos_Linhas)
        JOIN sissolucoes1.sis_produtos p ON beL.id_produto_sgs = p.id_produto
        JOIN sissolucoes1.sis_produtos_linhas pl ON p.cod_linha = pl.cod_linha
        WHERE beel.ean = '{$ean}'
        LIMIT 1";

        $query = $this->getArrayCollection($sql);
        if(!empty($query))
            $retorno = (object) $query[0];

        return $retorno;
    }

    public function matchProdutosDicionario($palavras)
    {
        $retorno = null;
        $sql = "
            SELECT d.id_produto_dicionario, COUNT(1) as qtdePalavra, x.qtde, d.id_produto, d.id_marca as id_marca_sgs, pl.id_linha
            FROM sissolucoes1.sis_produto_dicionario d
            INNER JOIN sissolucoes1.sis_produto_dicionario_palavra dpFind ON d.id_produto_dicionario = dpFind.id_produto_dicionario 
            INNER JOIN (
                SELECT id_produto_dicionario, COUNT(1) as qtde
                FROM sissolucoes1.sis_produto_dicionario_palavra
                WHERE palavra IN({$palavras})
                GROUP BY id_produto_dicionario
            ) AS x ON d.id_produto_dicionario = x.id_produto_dicionario
            LEFT JOIN sissolucoes1.sis_produtos p ON d.id_produto = p.id_produto
            LEFT JOIN sissolucoes1.sis_produtos_linhas pl ON p.cod_linha = pl.cod_linha
            GROUP BY d.id_produto_dicionario
            HAVING COUNT(1) = x.qtde
            #prioridade para quem tem mais palavras e tenha a marca e o produto especifico
            ORDER BY COUNT(1) DESC, IF(d.id_produto = 9999,0,1) + IF(d.id_marca = 9999,0,1) DESC
            LIMIT 1
        ";

        $query = $this->getArrayCollection($sql);
        if(!empty($query))
            $retorno = (object) $query[0];

        return $retorno;
    }

    public function matchProdutos($equipamento, $trataString = false, $marca = null)
    {
        $retorno = null;
        if ($trataString) $equipamento = $this->trata_string_match($this->aumenta_tamanho($equipamento));

        $where='';

        if (!empty($marca)) {
            $where .= " AND beM.nomeMarca like '%". str_replace("'", "\'", $marca) ."%' ";
            $id_marca = "beMSis.id_marca_sgs";
        } else {
            $id_marca = "9999";
        }

        $sql = "SELECT MATCH(beel.indexFulltext) against('{$equipamento}' IN BOOLEAN MODE) as indice, {$id_marca} AS id_marca_sgs, p.id_produto, pl.id_linha
        FROM business_engine.Equipamentos beel
        JOIN business_engine.Equipamentos_Marcas beM ON beel.idMarca = beM.idEquipamentos_Marcas
        JOIN business_engine.Equipamentos_Marcas_SIS beMSis ON beM.idEquipamentos_Marcas = beMSis.idMarca
        JOIN business_engine.Equipamentos_Linhas beL ON (beel.category = beL.idEquipamentos_Linhas OR beel.subCategory = beL.idEquipamentos_Linhas)
        JOIN sissolucoes1.sis_produtos p ON beL.id_produto_sgs = p.id_produto
        JOIN sissolucoes1.sis_produtos_linhas pl ON p.cod_linha = pl.cod_linha
        WHERE MATCH(beel.indexFulltext) AGAINST('{$equipamento}' IN BOOLEAN MODE) > 0
            {$where}
        ORDER BY 1 DESC
        LIMIT 1";

        $query = $this->getArrayCollection($sql);
        if(!empty($query))
            $retorno = (object) $query[0];

        return $retorno;
    }

    public function load_check_produto($marca,$modelo,$produto) {

        $id_produto="";

        // Check de Equipamento
        $res = $this->buscaProduto($produto);
        if($res){
            $id_produto = $res->id_produto;

            if ( $id_produto != "" )
                return $id_produto;
        }

        $res = $this->buscaProdutoDePara($produto);
        if($res){
            $id_produto_depara = $res->id_produto;
            $produto_para = $res->produto_para;

            if ( $id_produto_depara != "" ){
                $res = $this->buscaProduto($produto_para);
                if($res){
                    $id_produto = $res->id_produto;
                    $produto = $res->produto;

                    if ( $id_produto != "" )
                        return $id_produto;
                }
                return $id_produto;
            }
        }

        $MODELO = strtoupper($modelo);
        $DESCR_PRODUTO = strtoupper($produto);

        // tenta buscar produto no campo descricao 
        $res = $this->todosProdutos();
        if($res){
            foreach ($res as $row){
                $space = ( strlen($row["produto"]) < 4 ) ? ' ': '';
                $pos_modelo = strpos($space . $MODELO, $row["produto"]);
                $pos_produto = strpos($space . $DESCR_PRODUTO, $row["produto"]);

                if (!$pos_modelo === false || !$pos_produto === false){ 
                    $id_produto = $row["id_produto"];
                    $produto = $row["produto"];

                    if ($id_produto != "")
                        return $id_produto;
                }
            }
        }

        // tenta buscar produto por depara no campo descricao 
        $res = $this->todosProdutosDePara();
        if($res){
            foreach ($res as $row){
                $space = ( strlen($row["produto_de"]) < 4 ) ? ' ': '';
                $pos_modelo = strpos($space . $MODELO, $row["produto_de"]);
                $pos_produto = strpos($space . $DESCR_PRODUTO, $row["produto_de"]);

                if (!$pos_modelo === false || !$pos_produto === false){ 
                    $produto_para = $row["produto_para"] ;

                    $res2 = $this->buscaProduto($produto_para);
                    if($res2){
                        $id_produto = $res2->id_produto;
                        $produto = $res2->produto;

                        if ( $id_produto != "" )
                            return $id_produto;
                    }

                    
                }
            }
        }

        if ($id_produto==""){
            $id_produto = "9999";
            return $id_produto;
        }
    }



    public function todosProdutosDePara()
    {
        $retorno = null;
        return $this->getArrayCollection("select upper(produto_de) produto_de, upper(produto_para) produto_para, length(produto_de) tamanho from sissolucoes1.sis_produtos_depara order by tamanho desc");
    }


    public function buscaIdLinha($id_produto){
        $query = $this->getArrayCollection("select id_linha from sissolucoes1.sis_produtos_linhas l, sissolucoes1.sis_produtos p where p.id_produto = '{$id_produto}' and p.cod_linha = l.cod_linha;");

        if(!empty($query)) {
            $retorno = (object) $query[0];
            return $retorno->id_linha;
        }

        return null;
    }

    public function buscaMarcaDePara($marca)
    {
        $retorno = null;
        $query = $this->getArrayCollection("select id_marca , marca_para from sissolucoes1.sis_marcas_depara where upper(marca_de) = upper('$marca')");
        if(!empty($query))
            $retorno = (object) $query[0];

        return $retorno;
    }


    public function todasMarcas()
    {
        return $this->getArrayCollection("select id_marca, marca, length(marca) tamanho from sissolucoes1.sis_marcas order by tamanho desc");
    }

    public function trata_string_match($string){
        if(empty($string)) return $string;

        $string = trim($string);
        $string = str_replace(' e ', ' ', $string);

        $string = str_replace('\'', '', $string);
        $string = str_replace('(', '', $string);
        $string = str_replace(')', '', $string);
        $string = str_replace('-', ' ', $string);
        $string = str_replace('+', '', $string);
        $string = str_replace('*', '', $string);
        $string = str_replace('>', '', $string);
        $string = str_replace('<', '', $string);
        $string = str_replace('~', '', $string);
        $string = str_replace('"', '', $string);
        $string = preg_replace('/\s+/', '$1* ', $string);
        $string = preg_replace('/\s+[\W|\w]\s+/', '$1', ' '.$string.' ');

        #$string = preg_replace('/&([a-z])[a-z]+;/i', '$1', $string);
        $string = trim($string);
        $string .= (isset($string) && substr($string, -1) != "*") ? "*" : "";

        return $string;
    }


    public function aumenta_tamanho($string)

    {
        $array = explode(' ', $string);
        $valoresTotal   = "";
        $texto          = "";
    
        foreach( $array as $valores )
            {
                $texto = $valores;
                if( strlen($texto) == 2 && $texto !== "de" && $texto !== "c/"  && $texto !== "p/")
                    {
                        $texto = "XX" .$texto;
                    }
        $valoresTotal.= " " .$texto;
    
           }
    
        return $valoresTotal;
    }

    public function buscaProduto($produto)
    {
        $retorno = null;
        $query = $this->getArrayCollection("select id_produto, produto from sissolucoes1.sis_produtos where upper(produto) = upper('$produto')");
        if(!empty($query))
            $retorno = (object) $query[0];

        return $retorno;
    }

    public function buscaProdutoDePara($produto)
    {
        $retorno = null;
        $query = $this->getArrayCollection("select id_produto , produto_para from sissolucoes1.sis_produtos_depara where upper(produto_de) = upper('$produto')");
        if(!empty($query))
            $retorno = (object) $query[0];

        return $retorno;
    }

    public function todosProdutos()
    {
        $retorno = null;
        return $this->getArrayCollection("select id_produto, upper(produto) produto, length(produto) tamanho from sissolucoes1.sis_produtos order by tamanho desc");
    }

    private function getArrayCollection($sql){
        $result = $this->CI->db->query($sql)->result_array();
        return $result;
    }

    public function insert_mapfre_segurados_tmp($insertData){
        $this->CI->db->insert("sissolucoes1.sis_mapfre_segurados_tmp", $insertData);
        return $this->CI->db->insert_id();
    }

    public function insert_mapfre_equipamentos_tmp($insertData){
        $this->CI->db->insert("sissolucoes1.sis_mapfre_equipamentos_tmp", $insertData);
        return $this->CI->db->insert_id();
    }


    public function atualizarBaseMapfreTmp($id){
        $sqlSeguradosJaCadastrados = "
		SELECT DISTINCT seg.id_segurado,
	        c.nome_arquivo AS arquivo,
	        0 AS registro,
	        NOW() as data_carga,
	        if(a.cod_plano = 731, 1, if(a.cod_plano = 732, 33, if(a.cod_plano = 770, if(x.id_segurado IS NOT NULL, 268, 15), if(a.cod_plano = 405, 33, 0)))) AS id_contrato,
	        DATE_FORMAT(b.inicio_vigencia,'%Y-%m-%d') AS data_ini_cob,
	        DATE_FORMAT(b.final_vigencia,'%Y-%m-%d') AS data_fim_cob,
	        a.endereco_2 AS num_cert,
	        a.matricula AS apolice,
	        IF(b.id_linha = 0, 9999, b.id_linha) as id_linha,
	        b.id_equipamento_prod AS id_produto,
	        b.id_marca AS id_marca,
	        b.desc_prod AS modelo,
	        b.serie_prod AS num_serie,
	        b.nr_nf AS nota_num,
	        DATE_FORMAT(b.dt_compra,'%Y-%m-%d') AS nota_data,
	        b.vl_venda_bem AS nota_valor,
	        CASE WHEN LENGTH(TRIM(RIGHT(a.cpf,11))) = 11 THEN 'PF' ELSE 'PJ' END AS pfpj,
	        a.cpf AS seg_doc,
	        a.nome_cliente AS seg_nome,
	        a.endereco_1 AS seg_end_rua,
	        a.bairro AS seg_end_bai,
	        a.cep AS seg_end_cep,
	        a.municipio AS seg_end_cid,
	        a.uf AS seg_end_uf,
	        a.telefone AS seg_tel_fix,
	        (select cod_estipulante from sissolucoes1.sis_clientes_estipulantes j where j.cnpj_carga = a.cod_cliente_estipulante LIMIT 1) as cod_estipulante,
	        (select estipulante from sissolucoes1.sis_clientes_estipulantes k where k.cnpj_carga = a.cod_cliente_estipulante LIMIT 1) as estipulante,
	        a.n_loja AS loja,
	        a.nome_vendedor AS vendedor,
	        DATE_FORMAT(b.dt_compra,'%Y-%m-%d') AS data_vcto_orig,
	        UCASE(CONCAT(f.linha,'|',d.marca,'|',e.produto,'|',b.desc_prod)) AS lmem,
	        'SSIS_BBMAPFRE' as obs,
	        a.cod_plano as outros
        FROM sissolucoes1.sis_mapfre_segurados_tmp a
    		JOIN sissolucoes1.sis_mapfre_equipamentos_tmp b ON b.id_segurado = a.id_segurado
    		JOIN sisconnects.integracao_log c ON c.integracao_log_id = a.integracao_log_id
    		JOIN sissolucoes1.sis_marcas d ON d.id_marca = b.id_marca
    		JOIN sissolucoes1.sis_produtos e ON e.id_produto = b.id_equipamento_prod
    		LEFT JOIN sissolucoes1.sis_produtos_linhas f ON f.id_linha = b.id_linha
    		#POSSUI COBERTURA DE ROUBO/FURTO + QUEBRA
    		LEFT JOIN sissolucoes1.sis_mapfre_segurados_tmp x ON x.id_segurado = (
    			SELECT MAX(z.id_segurado)
    			FROM (
	    			SELECT y.id_segurado, y.cd_cobertura_cli
	    			FROM sissolucoes1.sis_mapfre_segurados_tmp x
	    			INNER JOIN sissolucoes1.sis_mapfre_equipamentos_tmp y ON x.id_segurado = y.id_segurado
	    			WHERE x.integracao_log_id = {$id} AND y.cd_cobertura_cli IN('447','6209')
					GROUP BY y.id_segurado, y.cd_cobertura_cli
    			) AS z
    			WHERE z.id_segurado = a.id_segurado
				GROUP BY z.id_segurado
				HAVING COUNT(1) > 1
    		)
    		JOIN sissolucoes11.sis_segurados seg ON (a.matricula = seg.apolice and seg.arquivo like 'SSIS_BBMAPFRE%' )
        WHERE a.tp_movi = 'I' and a.dt_pg = 0 and c.integracao_log_id = {$id}";
        
        $aSeguradosJaCadastrados = $this->getArrayCollection($sqlSeguradosJaCadastrados);

        foreach($aSeguradosJaCadastrados as $row){
            $sqlUPDATESegurado = "UPDATE sissolucoes11.sis_segurados SET
                caminho  =  'ATUALIZACAO DE REGISTRO',
                arquivo  =  '{$row['arquivo']}',
                registro  =  '{$row['registro']}',
                data_carga  =  '{$row['data_carga']}',
                id_contrato  =  '{$row['id_contrato']}',
                data_ini_cob  =  '{$row['data_ini_cob']}',
                data_fim_cob  =  '{$row['data_fim_cob']}',
                num_cert  =  '{$row['num_cert']}',
                apolice  =  '{$row['apolice']}',
                id_linha  =  '{$row['id_linha']}',
                id_produto  =  '{$row['id_produto']}',
                id_marca  =  '{$row['id_marca']}',
                modelo  =  '{$row['modelo']}',
                num_serie  =  '{$row['num_serie']}',
                nota_num  =  '{$row['nota_num']}',
                nota_data  =  '{$row['nota_data']}',
                nota_valor  =  '{$row['nota_valor']}',
                pfpj  =  '{$row['pfpj']}',
                seg_doc  =  '{$row['seg_doc']}',
                seg_nome  =  '{$row['seg_nome']}',
                seg_end_rua  =  '{$row['seg_end_rua']}',
                seg_end_bai  =  '{$row['seg_end_bai']}',
                seg_end_cep  =  '{$row['seg_end_cep']}',
                seg_end_cid  =  '{$row['seg_end_cid']}',
                seg_end_uf  =  '{$row['seg_end_uf']}',
                seg_tel_fix  =  '{$row['seg_tel_fix']}',
                cod_estipulante  =  '{$row['cod_estipulante']}',
                estipulante  =  '{$row['estipulante']}',
                loja  =  '{$row['loja']}',
                vendedor  =  '{$row['vendedor']}',
                data_vcto_orig  =  '{$row['data_vcto_orig']}',
                lmem  =  '{$row['lmem']}',
                obs  =  '{$row['obs']}',
                outros   =  '{$row['outros']}'
            WHERE
                id_segurado = {$row['id_segurado']}";

            $this->CI->db->query($sqlUPDATESegurado);
        }


		#TRECHO PARA INCLUIR SER SEGURADOS NA TABELA DEFINITAVA
		$sqlInsert = "
		INSERT INTO sissolucoes11.sis_segurados (caminho, arquivo,registro, data_carga, id_contrato, data_ini_cob, data_fim_cob, num_cert, apolice, id_linha, id_produto, id_marca, modelo, num_serie, nota_num, nota_data, nota_valor, pfpj, seg_doc, seg_nome, seg_end_rua,  seg_end_bai, seg_end_cep, seg_end_cid, seg_end_uf, seg_tel_fix, cod_estipulante, estipulante, loja, vendedor, data_vcto_orig, lmem, obs, outros)
		SELECT DISTINCT 'inclusao nova carga', c.nome_arquivo AS arquivo, 0,  NOW(),
		if(a.cod_plano = 731, 1, if(a.cod_plano = 732, 33, if(a.cod_plano = 770, if(x.id_segurado IS NOT NULL, 268, 15), if(a.cod_plano = 405, 33, 0)))), DATE_FORMAT(b.inicio_vigencia,'%Y-%m-%d'), DATE_FORMAT(b.final_vigencia,'%Y-%m-%d'), a.endereco_2, a.matricula, IF(b.id_linha = 0, 9999, b.id_linha) as id_linha, b.id_equipamento_prod, b.id_marca, b.desc_prod, b.serie_prod, b.nr_nf, DATE_FORMAT(b.dt_compra,'%Y-%m-%d'), b.vl_venda_bem, CASE WHEN LENGTH(TRIM(RIGHT(a.cpf,11))) = 11 THEN 'PF' ELSE 'PJ' END AS pfpj, a.cpf,   a.nome_cliente, a.endereco_1, a.bairro, a.cep, a.municipio, a.uf, a.telefone, (select cod_estipulante from sissolucoes1.sis_clientes_estipulantes j where j.cnpj_carga = a.cod_cliente_estipulante LIMIT 1) as cod_estipulante,(select estipulante from sissolucoes1.sis_clientes_estipulantes k where k.cnpj_carga = a.cod_cliente_estipulante LIMIT 1) as estipulante, a.n_loja, a.nome_vendedor, DATE_FORMAT(b.dt_compra,'%Y-%m-%d'), UCASE(CONCAT(f.linha,'|',d.marca,'|',e.produto,'|',b.desc_prod)) AS lmem, 'SSIS_BBMAPFRE' as obs, a.cod_plano as outros
		FROM sissolucoes1.sis_mapfre_segurados_tmp a
		JOIN sissolucoes1.sis_mapfre_equipamentos_tmp b ON b.id_segurado = a.id_segurado
		JOIN sisconnects.integracao_log c ON c.integracao_log_id = a.integracao_log_id
		JOIN sissolucoes1.sis_marcas d ON d.id_marca = b.id_marca
		JOIN sissolucoes1.sis_produtos e ON e.id_produto = b.id_equipamento_prod
		LEFT JOIN sissolucoes1.sis_produtos_linhas f ON f.id_linha = b.id_linha
		#POSSUI COBERTURA DE ROUBO/FURTO + QUEBRA
		LEFT JOIN sissolucoes1.sis_mapfre_segurados_tmp x ON x.id_segurado = (
			SELECT MAX(z.id_segurado)
			FROM (
    			SELECT y.id_segurado, y.cd_cobertura_cli
    			FROM sissolucoes1.sis_mapfre_segurados_tmp x
    			INNER JOIN sissolucoes1.sis_mapfre_equipamentos_tmp y ON x.id_segurado = y.id_segurado
    			WHERE x.integracao_log_id = {$id} AND y.cd_cobertura_cli IN('447','6209')
				GROUP BY y.id_segurado, y.cd_cobertura_cli
			) AS z
			WHERE z.id_segurado = a.id_segurado
			GROUP BY z.id_segurado
			HAVING COUNT(1) > 1
		)
		WHERE
		    (
                c.nome_arquivo not in (select ss1.arquivo from sissolucoes11.sis_segurados ss1 where ss1.arquivo like 'SSIS_BBMAPFRE%') OR
		      a.matricula NOT IN (select ss2.apolice from sissolucoes11.sis_segurados ss2 where ss2.arquivo like 'SSIS_BBMAPFRE%' and ss2.apolice is not null)
		    )
		 and a.tp_movi = 'I'
         and a.dt_pg = 0
         and c.integracao_log_id = {$id}
         order by a.id_segurado desc";

        $this->CI->db->query($sqlInsert);


		#TRECHO PARA CANCELAR SEGURADOS
		$sqlUpdate = "select a.cpf, trim(a.matricula) as apolice, a.tp_movi as tipo_movimento
		FROM sissolucoes1.sis_mapfre_segurados_tmp a
		JOIN sissolucoes1.sis_mapfre_equipamentos_tmp b ON b.id_segurado = a.id_segurado
		JOIN sisconnects.integracao_log c ON c.integracao_log_id = a.integracao_log_id
		JOIN sissolucoes1.sis_marcas d ON d.id_marca = b.id_marca
		JOIN sissolucoes1.sis_produtos e ON e.id_produto = b.id_equipamento_prod
		LEFT JOIN sissolucoes1.sis_produtos_linhas f ON f.id_linha = b.id_linha
		where a.tp_movi = 'E'
        and a.integracao_log_id = {$id}";

        $aUpdate = $this->getArrayCollection($sqlUpdate);

        foreach($aUpdate as $row){
            $tipo_movimento = $row['tipo_movimento'];
			$seg_doc = $row['cpf'];
			$apolice = $row['apolice'];

			if ($tipo_movimento == 'E') { //grava data de exclusao, inclui data de baixa
				$this->CI->db->query("update sissolucoes11.sis_segurados set data_baixa = sysdate()
				where seg_doc = '$seg_doc' and apolice = '$apolice' ");				
			}
        }

		#TRECHO PARA INCLUIR PREMIO NA TABELA SIS_SEGURADOS_COMPLEMENTO
		$sqlPremio = "insert into sissolucoes11.sis_segurados_complemento (id_segurado,valor_prestacao,data_ini_vigencia,tipo_prestacao_expediente,id_contrato_cobertura,cd_cobertura_cli,desc_cobertura_cli)
		select c.id_segurado, b.vl_premio, c.data_ini_cob, case when b.tipo_plano = 0 then 'Y' else case when b.tipo_plano = 1 then 'R' end end as tipo_plano, IF(c.id_contrato <> 268, NULL, IF(b.cd_cobertura_cli = '447', 1879, IF(b.cd_cobertura_cli = '6209', 1881, b.id_contrato_cobertura))), b.cd_cobertura_cli, b.desc_cobertura_cli
		from sissolucoes1.sis_mapfre_segurados_tmp a
		join sissolucoes1.sis_mapfre_equipamentos_tmp b on a.id_segurado = b.id_segurado
		join sissolucoes11.sis_segurados c on c.seg_doc = a.cpf and a.matricula = c.apolice
		left join sissolucoes11.sis_segurados_complemento sc on c.id_segurado = sc.id_segurado AND sc.id_contrato_cobertura = IF(c.id_contrato <> 268, NULL, IF(b.cd_cobertura_cli = '447', 1879, IF(b.cd_cobertura_cli = '6209', 1881, b.id_contrato_cobertura)))
		where sc.id_segurado is null
        and a.integracao_log_id = $id";
        
        $this->CI->db->query($sqlPremio);

    }
    
    
    
    

    public function getInseridosMapfre($integracao_log_id){
        $SQL = "SELECT 
            IF(seg.tp_movi = 'I', 1, 9) AS acao,
            seg.endereco_2 AS num_apolice,
            equip.nr_nf AS nota_fiscal_numero,
            seg.nome_cliente AS nome,
            seg.telefone,
            seg.endereco_1 AS endereco_logradouro,
            seg.bairro AS endereco_bairro,
            seg.municipio AS endereco_cidade,
            seg.uf AS endereco_estado,
            seg.cep AS endereco_cep,
            seg.cpf,
            SUM(equip.vl_premio) AS premio_bruto,
            equip.marca_prod AS marca,
            equip.modelo_prod AS modelo,
            equip.desc_prod AS equipamento_nome,
            equip.serie_prod AS num_serie,
            equip.dt_compra AS nota_fiscal_data,
            equip.inicio_vigencia AS data_inicio_vigencia,
            equip.final_vigencia AS data_fim_vigencia,
            equip.vl_venda_bem AS nota_fiscal_valor,
            ild.integracao_log_detalhe_id,
            if(x.id_segurado IS NOT NULL, 135, 136) AS produto_parceiro_id,
            if(x.id_segurado IS NOT NULL, 216, 217) AS produto_parceiro_plano_id,
            equip.dt_compra AS data_adesao_cancel,
            '' AS ean

        FROM 
            sissolucoes1.sis_mapfre_segurados_tmp AS seg

        INNER JOIN 
            sissolucoes1.sis_mapfre_equipamentos_tmp AS equip
            ON seg.id_segurado = equip.id_segurado

        INNER JOIN
            sisconnects.integracao_log_detalhe AS ild
            ON ild.integracao_log_id = seg.integracao_log_id
            AND ild.chave = CONCAT(seg.endereco_2, '|', IF(seg.tp_movi = 'I', 'EF', 'CN'))

        LEFT JOIN sissolucoes1.sis_mapfre_segurados_tmp x ON x.id_segurado = (
			SELECT MAX(z.id_segurado)
			FROM (
    			SELECT y.id_segurado, y.cd_cobertura_cli
    			FROM sissolucoes1.sis_mapfre_segurados_tmp x
    			INNER JOIN sissolucoes1.sis_mapfre_equipamentos_tmp y ON x.id_segurado = y.id_segurado
    			WHERE x.integracao_log_id = {$integracao_log_id} AND y.cd_cobertura_cli IN('447','6209')
				GROUP BY y.id_segurado, y.cd_cobertura_cli
			) AS z
			WHERE z.id_segurado = seg.id_segurado
			GROUP BY z.id_segurado
			HAVING COUNT(1) > 1
		)

        WHERE 1 = 1
            AND seg.integracao_log_id = $integracao_log_id
            GROUP BY seg.id_segurado";

        return $this->getArrayCollection($SQL);
    }



}