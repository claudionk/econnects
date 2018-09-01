<?php
  $sapi_type = php_sapi_name();

  //if( $sapi_type != "cli" ) {
  //  die();
  //}

  date_default_timezone_set("America/Sao_Paulo");
  require_once("/var/www/webroot/ROOT/integracao/PHPExcel/Classes/PHPExcel.php");

  //$conn = new mysqli( "191.243.199.213", "root", "SITecq77218", "orangehrm" );

  $dictionary = "/var/www/webroot/ROOT/integracao/lasa/Dicionario-Clientes.xlsx";
  $type = PHPExcel_IOFactory::identify($dictionary);
  $objReader = PHPExcel_IOFactory::createReader($type);
  $objPHPExcel = $objReader->load($dictionary);
  $sheets = $objPHPExcel->getSheetNames();

  $original_filename = "ME2119597741TR20180421.txt";
  echo "<h2>Arquivo Original ($original_filename)</h2>";
  echo "<pre>";
  $retorno = "";
  foreach( $sheets as $sheet ) {
    if( strtoupper( $sheet ) == "HEADER" ) {
      $headers = [];
      $objPHPExcel->setActiveSheetIndexByName( $sheet );
      $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();
      foreach($rowIterator as $row){
        $cellIterator = $row->getCellIterator();
        if( $row->getRowIndex() >= 2 ) {
          $field_name = trim( $objPHPExcel->getActiveSheet()->getCell("A". $row->getRowIndex())->getCalculatedValue() );
          $field_description = trim( $objPHPExcel->getActiveSheet()->getCell("B". $row->getRowIndex())->getCalculatedValue() );
          $pos = trim( $objPHPExcel->getActiveSheet()->getCell("C". $row->getRowIndex())->getCalculatedValue() );
          $size = trim( $objPHPExcel->getActiveSheet()->getCell("D". $row->getRowIndex())->getCalculatedValue() );
          $type = trim( $objPHPExcel->getActiveSheet()->getCell("E". $row->getRowIndex())->getCalculatedValue() );
          $format = trim( $objPHPExcel->getActiveSheet()->getCell("F". $row->getRowIndex())->getCalculatedValue() );
          $hint = trim( $objPHPExcel->getActiveSheet()->getCell("G". $row->getRowIndex())->getCalculatedValue() );
          $headers[] = array( "name" => $field_name, "description" => $field_description, "pos" => $pos, "size" => $size, "type" => $type, "format" => $format, "hint" => $hint );
        }
      }
      $handle = @fopen("/var/www/webroot/ROOT/integracao/lasa/in/$original_filename", "r");
      if($handle) {
        $hcontent = [];
        $linha = 1;
        while( ($buffer = fgets( $handle, 4096 ) ) !== false ) {
          $buffer = str_replace( "\n", "", $buffer );
          $buffer = str_replace( "\r", "", $buffer );
          $msg_erro = "";
          foreach( $headers as $field ) {
            $hcontent[$field["name"]] = substr( $buffer, $field["pos"], $field["size"] );
            if( $field["hint"] != "" ) {
              if( str_pad( $hcontent[$field["name"]], $field["size"], " " ) != str_pad( $field["hint"], $field["size"], " " ) ) {
                $msg_erro = " VALOR ESPERADO: " . $field["hint"] . " VALOR OBTIDO: " . $hcontent[$field["name"]];
              }
            }
            if( $field["type"] == "N" ) {
              if( !is_numeric( $hcontent[$field["name"]] ) ) {
                $msg_erro = " VALOR ESPERADO: NUMERIC VALOR OBTIDO: " . $hcontent[$field["name"]];
              }
            }
            if( $field["type"] == "D" ) {
              if( !is_valid_date( $hcontent[$field["name"]], $field["format"] ) ) {
                $msg_erro = " VALOR ESPERADO: DATE(" . $field["format"] . ") VALOR OBTIDO: " . $hcontent[$field["name"]];
              }
            }
          }
          $linha = $linha + 1;
          if( $linha > 1 ) {
            break;
          }
        }
        if( $msg_erro == "" ) {
          $retorno .= $buffer . " OK\n";
          echo $buffer . " OK\n";
        } else {
          $retorno .= $buffer . " ERR HEADER LINHA: " . str_pad( $linha, 4, "0", STR_PAD_LEFT ) . " POS: " . str_pad( $field["pos"], 4, "0", STR_PAD_LEFT ) . " CAMPO: " . $field["name"] . " $msg_erro\n";
          echo $buffer . " ERR HEADER LINHA: " . str_pad( $linha, 4, "0", STR_PAD_LEFT ) . " POS: " . str_pad( $field["pos"], 4, "0", STR_PAD_LEFT ) . " CAMPO: " . $field["name"] . " $msg_erro\n";
        }
        fclose( $handle );
      }
    }
    if( strtoupper( $sheet ) == "DETAIL" ) {
      $headers = [];
      $objPHPExcel->setActiveSheetIndexByName( $sheet );
      $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();
      foreach($rowIterator as $row){
        $cellIterator = $row->getCellIterator();
        if( $row->getRowIndex() >= 2 ) {
          $field_name = trim( $objPHPExcel->getActiveSheet()->getCell("A". $row->getRowIndex())->getCalculatedValue() );
          $field_description = trim( $objPHPExcel->getActiveSheet()->getCell("B". $row->getRowIndex())->getCalculatedValue() );
          $pos = trim( $objPHPExcel->getActiveSheet()->getCell("C". $row->getRowIndex())->getCalculatedValue() );
          $size = trim( $objPHPExcel->getActiveSheet()->getCell("D". $row->getRowIndex())->getCalculatedValue() );
          $type = trim( $objPHPExcel->getActiveSheet()->getCell("E". $row->getRowIndex())->getCalculatedValue() );
          $format = trim( $objPHPExcel->getActiveSheet()->getCell("F". $row->getRowIndex())->getCalculatedValue() );
          $hint = trim( $objPHPExcel->getActiveSheet()->getCell("G". $row->getRowIndex())->getCalculatedValue() );
          $headers[] = array( "name" => $field_name, "description" => $field_description, "pos" => $pos, "size" => $size, "type" => $type, "format" => $format, "hint" => $hint );
        }
      }
      $handle = @fopen("/var/www/webroot/ROOT/integracao/lasa/in/$original_filename", "r");
      if($handle) {
        $dcontent = [];
        $linha = 1;
        while( ($buffer = fgets( $handle, 4096 ) ) !== false ) {
          $buffer = str_replace( "\n", "", $buffer );
          $buffer = str_replace( "\r", "", $buffer );
          $msg_erro = "";
          if( $linha > 1 ) {
            if( substr( $buffer, 0, 2 ) == "03" ) {
              break;
            }
            foreach( $headers as $field ) {
              if( is_numeric( $field["pos"] ) != "" ) {
                $dcontent[$field["name"]] = substr( $buffer, $field["pos"], $field["size"] );
                if( $field["hint"] != "" ) {
                  if( str_pad( $dcontent[$field["name"]], $field["size"], " " ) != str_pad( $field["hint"], $field["size"], " " ) ) {
                    $msg_erro = " VALOR ESPERADO: " . $field["hint"] . " VALOR OBTIDO: " . $dcontent[$field["name"]];
                    break;
                  }
                }
                if( $field["type"] == "N" ) {
                  if( !is_numeric( $dcontent[$field["name"]] ) ) {
                    $msg_erro = " VALOR ESPERADO: NUMERIC VALOR OBTIDO: " . $dcontent[$field["name"]];
                    break;
                  }
                }
                if( $field["type"] == "D" ) {
                  if( !is_valid_date( $dcontent[$field["name"]], $field["format"] ) ) {
                    $msg_erro = " VALOR ESPERADO: DATE(" . $field["format"] . ") VALOR OBTIDO: " . $dcontent[$field["name"]];
                    break;
                  }
                }
              }
            }
            $content[] = $dcontent;

            if( $msg_erro == "" ) {
              $retorno .= $buffer . " OK\n";
              echo $buffer . " OK\n";
            } else {
              $retorno .= $buffer . " ERR DETAIL LINHA: " . str_pad( $linha, 4, "0", STR_PAD_LEFT ) . " POS: " . str_pad( $field["pos"], 4, "0", STR_PAD_LEFT ) . " CAMPO: " . $field["name"] . " $msg_erro\n";
              echo $buffer . " ERR DETAIL LINHA: " . str_pad( $linha, 4, "0", STR_PAD_LEFT ) . " POS: " . str_pad( $field["pos"], 4, "0", STR_PAD_LEFT ) . " CAMPO: " . $field["name"] . " $msg_erro\n";
            }
          }
          $linha = $linha + 1;
        }
        $retorno .= $buffer . " OK\n";
        echo $buffer . " OK\n";
        echo "</pre>";
        fclose( $handle );
        file_put_contents( "/var/www/webroot/ROOT/integracao/lasa/out/RET_$original_filename", $retorno );
        echo "<h2>Arquivo de Retorno</h2><pre>";
        echo "<a href=\"/integracao/lasa/out/RET_$original_filename\">RET_$original_filename</a>";
        echo "</pre>";
        echo "<h2>Antes</h2><pre>";
        print_r( $content );
        echo "</pre>";
        $key = 0;
        foreach( $content as $row ) {
          $CPF = substr( $row["CPF"], -11, 11 ); 
          // Processa só os novos seguros
          if( $row["Tipo de Transação"] == "NS" ) {
            $iFaroCredentials = getIFaroToken();
            $iFaroExtraInfo = getIFaroExtraInfo( $CPF, $iFaroCredentials->{"Token"} );

            $ExtraTelefones = $iFaroExtraInfo->{"Telefones"};
            if( sizeof( $ExtraTelefones ) ) {
              $content["$key"]["Número do telefone"] = $ExtraTelefones[0]->{"DD"}.$ExtraTelefones[0]->{"Numero"};
            }

            $ExtraEnderecos = $iFaroExtraInfo->{"Enderecos"};
            if( sizeof( $ExtraEnderecos ) ) {
              $content["$key"]["Endereço"] = $ExtraEnderecos[0]->{"Logadouro"} . ", " . $ExtraEnderecos[0]->{"Numero"};
              if( $ExtraEnderecos[0]->{"Complemento"} != "" ) {
                $content["$key"]["Endereço"] .= " - " . $ExtraEnderecos[0]->{"Complemento"};
              }
              $content["$key"]["Endereço"] .= " - " . $ExtraEnderecos[0]->{"Bairro"} . " - " . $ExtraEnderecos[0]->{"Cidade"} . " - " . $ExtraEnderecos[0]->{"UF"} . " - " . $ExtraEnderecos[0]->{"CEP"};
            }
            $content["$key"]["Endereço"] = substr( $content["$key"]["Endereço"], 0, 45 );

            $content[$key]["Sexo"] = $iFaroExtraInfo->{"Sexo"};
            $content[$key]["DataNascimento"] = $iFaroExtraInfo->{"DataNascimento"};
            $content[$key]["Mae"] = $iFaroExtraInfo->{"Mae"};

            $ean = substr( $content[$key]["Cód do EAN"], -13 );
            $EquipamentoExtraInfo = getEquipamentoExtraInfo( $ean );
            $EquipamentoExtraInfo = $EquipamentoExtraInfo->{"Equipamento"};
            //print_r( $EquipamentoExtraInfo );
            $content[$key]["Marca"] = strtoupper( $EquipamentoExtraInfo->{"brand"} );
            $content[$key]["Modelo"] = substr( strtoupper( $EquipamentoExtraInfo->{"name"} ), 0, 80 );
            $key = $key + 1;
          }
        }
        echo "<h2>Depois</h2><pre>";
        print_r( $content );
        echo "</pre>";

        echo "<h2>Arquivo enriquecido</h2><pre>\n";
        
        // Gera arquivo enriquecido
        $objPHPExcel->setActiveSheetIndexByName( "Detail" );
        $ri = $objPHPExcel->getActiveSheet()->getRowIterator();
        $field_content = "";
        foreach( $content as $rec ) {
          echo "\n\nInicio:\n";
          foreach( $ri as $rw ) {
            if( $rw->getRowIndex() >= 2 ) {
              $field_name = trim( $objPHPExcel->getActiveSheet()->getCell("A". $rw->getRowIndex())->getCalculatedValue() );
              $field_description = trim( $objPHPExcel->getActiveSheet()->getCell("B". $rw->getRowIndex())->getCalculatedValue() );
              $pos = trim( $objPHPExcel->getActiveSheet()->getCell("C". $rw->getRowIndex())->getCalculatedValue() );
              $size = trim( $objPHPExcel->getActiveSheet()->getCell("D". $rw->getRowIndex())->getCalculatedValue() );
              $type = trim( $objPHPExcel->getActiveSheet()->getCell("E". $rw->getRowIndex())->getCalculatedValue() );
              $format = trim( $objPHPExcel->getActiveSheet()->getCell("F". $rw->getRowIndex())->getCalculatedValue() );
              $hint = trim( $objPHPExcel->getActiveSheet()->getCell("G". $rw->getRowIndex())->getCalculatedValue() );
              $headers = array( "name" => $field_name, "description" => $field_description, "pos" => $pos, "size" => $size, "type" => $type, "format" => $format, "hint" => $hint );

              if( $headers["pos"] != "" ) {
                echo $rec[$headers["name"]] . "\n";
                if( $headers["type"] == "T" ) {
                  $field_content .= str_pad( $rec[$headers["name"]], $headers["size"], " ", STR_PAD_RIGHT );
                  echo $field_content;
                  //die();
                }          
                if( $header["type"] == "N" ) {
                  $field_content .= str_pad( $rec[$headers["name"]], $headers["size"], "0", STR_PAD_LEFT );
                  echo $field_content;
                }          
                if( $header["type"] == "D" ) {
                  $field_content .= str_pad( date( $rec[$headers["name"]], $headers["format"] ), $headers["size"], "0", STR_PAD_LEFT );
                  echo $field_content;
                  //die();
                }
              }
            }
          }
          echo "\n\n\n";
        }
      }
    }
  }

  function is_valid_date($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
  }

  function getIFaroToken() {
    $Login = base64_encode("SISADM");
    $Senha = base64_encode("sis@2018");
    $Url = "http://ws.ifaro.com.br//Seguranca.svc/API/GetToken/$Login/$Senha";

    $myCurl = curl_init();
    curl_setopt( $myCurl, CURLOPT_URL, $Url );
    curl_setopt( $myCurl, CURLOPT_FRESH_CONNECT, 1 );
    curl_setopt( $myCurl, CURLOPT_POST, 0 );
    curl_setopt( $myCurl, CURLOPT_VERBOSE, 0);
    curl_setopt( $myCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt( $myCurl, CURLOPT_TIMEOUT, 15 );
    curl_setopt( $myCurl, CURLOPT_CONNECTTIMEOUT, 15 );
    $Response = curl_exec( $myCurl );
    curl_close( $myCurl );
    header("Content-Type: application/json");
    return( json_decode( $Response ) );
  }

  function getIFaroExtraInfo( $CPF, $Token ) {
    $Url = "http://ws.ifaro.com.br/APIDados.svc/ConsultaPessoa/$CPF/$Token";
    $myCurl = curl_init();
    curl_setopt( $myCurl, CURLOPT_URL, $Url );
    curl_setopt( $myCurl, CURLOPT_FRESH_CONNECT, 1 );
    curl_setopt( $myCurl, CURLOPT_POST, 0 );
    curl_setopt( $myCurl, CURLOPT_VERBOSE, 0);
    curl_setopt( $myCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt( $myCurl, CURLOPT_TIMEOUT, 15 );
    curl_setopt( $myCurl, CURLOPT_CONNECTTIMEOUT, 15 );
    $Response = curl_exec( $myCurl );
    curl_close( $myCurl );
    header("Content-Type: application/json");
    return( json_decode( $Response ) );
  }

  function getEquipamentoExtraInfo( $ean ) {
    $Url = "https://sgs-h.jelastic.saveincloud.net/v1/api/compras/equipamento/$ean";
    $myCurl = curl_init();
    curl_setopt( $myCurl, CURLOPT_URL, $Url );
    curl_setopt( $myCurl, CURLOPT_FRESH_CONNECT, 1 );
    curl_setopt( $myCurl, CURLOPT_USERPWD, "bm2:sis123");
    curl_setopt( $myCurl, CURLOPT_POST, 0 );
    curl_setopt( $myCurl, CURLOPT_VERBOSE, 0);
    curl_setopt( $myCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt( $myCurl, CURLOPT_TIMEOUT, 15 );
    curl_setopt( $myCurl, CURLOPT_CONNECTTIMEOUT, 15 );
    $Response = curl_exec( $myCurl );
    curl_close( $myCurl );
    header("Content-Type: application/json");
    return( json_decode( $Response ) );
  }



?>
