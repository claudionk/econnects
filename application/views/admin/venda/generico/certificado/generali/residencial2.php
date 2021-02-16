<?php
//print_pre($coberturas_all);
$aGeneraliResidenciaCobertura = array();

foreach ($coberturas_all as $i => $cobertura) {
    
    $cobertura_slug         = $cobertura["cobertura_slug"];
    $assistencia            = $cobertura["assistencia"];
    $cobertura_plano_descricao         = $cobertura["cobertura_plano_descricao"];
    $importancia_segurada   = (float) $cobertura["importancia_segurada"];

    $data = array();
    $data["cobertura_plano_descricao"]         = $cobertura_plano_descricao;
    $data["importancia_segurada"]   = $importancia_segurada;

    if ($assistencia == 0) {

        if(in_array($cobertura_slug, array("incendio", "queda_raio", "explosao"))){
            $cobertura_slug = "incendio_queda_raio_explosao";            
        }

        if (!isset($aGeneraliResidenciaCobertura[$cobertura_slug])) {            
            $aGeneraliResidenciaCobertura[$cobertura_slug] = $data;
        }
        
    }

}

$aCoberturaDescricao = array(
    "incendio_queda_raio_explosao" => array(
        "descricao" => "Esta cobertura consiste no pagamento de indenização até o limite máximo de {coberturaDescricao_valor} por danos materiais diretamente causados por incêndio, inclusive fumaça proveniente de incêndio ocorrido dentro ou fora do terreno onde se localiza o imóvel; Queda de raio ocorrida dentro da área do terreno/imóvel onde estiverem
        localizados os bens segurados; Explosão de gás, ocorrida dentro da área do terreno/imóvel onde estiverem
        localizados os bens segurados, contanto que o gás não tenha sido gerado no(s) local(is) segurado(s) ou que este(s)
        não faça(m) parte de qualquer fábrica de gás."
    ),
    "danos-eletricos" => array(
        "descricao" => "Esta cobertura consiste no pagamento de indenização até o limite máximo de {coberturaDescricao_valor} por perdas e/ou danos físicos diretamente causados a quaisquer máquinas, equipamentos ou instalações eletrônicas ou elétricas devido a variações anormais de tensão, curto-circuito, arco voltaico, calor gerado acidentalmente por eletricidade, descargas elétricas, eletricidade estática ou qualquer efeito ou fenômeno de natureza elétrica, inclusive a queda de raio ocorrida fora do local segurado."
    ),
    "vendaval" => array(        
        "descricao" => "Esta cobertura consiste no pagamento de indenização até o limite máximo de {coberturaDescricao_valor} por danos materiais diretamente causados pela ocorrência de vendaval, furacão, ciclone, tornado e/ou granizo."
    ),
    "pagto_aluguel" => array(        
        "descricao" => "Esta cobertura consiste no reembolso ao Segurado das quantias pelas quais vier a ser responsável civilmente em sentença judicial transitada em julgado ou em acordo autorizado de modo expresso pela Seguradora, até o limite máximo de {coberturaDescricao_valor}, relativas a reparações por danos involuntários, corporais ou materiais causados a terceiros, durante a vigência do microsseguro, pelo próprio Segurado, seu cônjuge, filhos menores que estiverem em seu poder ou em sua companhia, por animais domésticos cuja posse o Segurado detenha e pela queda de objetos ou seu lançamento em lugar indevido."
    ),
    "roubo" => array(
        "nome" => "ROUBO E/OU FURTO QUALIFICADO",
        "descricao" => "Esta cobertura consiste no pagamento de indenização até o limite máximo de {coberturaDescricao_valor} por danos materiais diretamente causados por roubo ou furto qualificado dos bens de propriedade do Segurado no interior do imóvel, pelos prejuízos materiais causados ao imóvel ou seu conteúdo durante a prática do roubo ou furto qualificado, ou mesmo pela sua simples tentativa. Para efeitos desta cobertura considera-se:",
        "sub" => array(
            array(
                "nome" => "Roubo",
                "descricao" => "subtração de coisa alheia móvel, mediante grave ameaça ou violência praticada contra a pessoa, ou após redução da possibilidade de defesa ou resistência da pessoa."
            ),
            array(
                "nome" => "Furto Qualificado",
                "descricao" => "subtração de coisa alheia móvel, mediante a destruição e/ou o rompimento de algum obstáculo que impedia o acesso à coisa alheia móvel e/ou mediante escalada ou destreza; ou ainda quando a subtração é feita com abuso de confiança ou através de quaisquer artifícios usados para enganar a confiança da vítima; ou quando a subtração é realizada com o uso de qualquer instrumento, que não a verdadeira chave, para abrir fechaduras; ou quando a subtração é praticada por duas ou mais pessoas."
            )
        )
    )
);


$html = "<ul>";
foreach($aGeneraliResidenciaCobertura as $slug => $cobertura){
    
    $nome = $cobertura["cobertura_plano_descricao"];
    $importancia_segurada = "R$ ".app_format_currency($cobertura['importancia_segurada']);
    //$slug = $cobertura["cobertura_slug"];

    $coberturaDescricao = $aCoberturaDescricao[$slug];
    if(isset($coberturaDescricao["nome"])){
        $nome = $coberturaDescricao["nome"];
    }

    $descricao = str_replace("{coberturaDescricao_valor}", $importancia_segurada, $coberturaDescricao["descricao"]);

    $html .= "<li><b>$nome: </b>$descricao</li>";
    if(isset($coberturaDescricao["sub"])){
        $aSub = $coberturaDescricao["sub"];
        foreach($aSub as $sub){
            $sub_nome = $sub["nome"];
            $sub_descricao = $sub["descricao"];
            $html .= "<li><b>$sub_nome: </b>$sub_descricao</li>";
        }
    }

}

$html .= "</ul>";

echo $html;