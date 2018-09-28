<?php
  $EnabledTransactionTypes = [];
  $EnabledDebitBrands = [];
  $EnabledCreditBrands = [];
  $EnabledInstallments = [];

  foreach( $forma_pagamento as $k => $v ) {
    if( $v["tipo"]["slug"] == "cartao_credito" ) {
      $EnabledTransactionTypes[] = "C";
      if( $v["bandeiras"] ) {
        foreach( $v["bandeiras"] as $bc ) {
        $EnabledCreditBrands[] = strtolower( $bc["nome"] );
        }
        if( isset( $v["pagamento"][0]["parcelamento"] ) ) {
          $Installments = $v["pagamento"][0]["parcelamento"];
          for( $i = 1; $i <= $v["pagamento"][0]["parcelamento_maximo_sem_juros"]; $i++ ) {
            $EnabledInstallments[] = $i;
          }
        }
      }
    }
    if( $v["tipo"]["slug"] == "cartao_debito" ) {
      $EnabledTransactionTypes[] = "D";
      if( $v["bandeiras"] ) {
        foreach( $v["bandeiras"] as $bd ) {
          $EnabledDebitBrands[] = strtolower( $bd["nome"] );
        }
      }
    }
    if( $v["tipo"]["slug"] == "boleto_pagmax" ) {
      $EnabledTransactionTypes[] = "B";
    }
  }


  //print_r( $EnabledTransactionTypes );

  $Payload = array( 
    "Customer" => array( 
      "Identification" => "",
      "Name" => $cotacao["nome"],
      "Email" => $cotacao["email"],
      "PhoneNumber" => preg_replace( "/[^0-9]/", "", $cotacao["telefone"] )
    ),
    "Sale" => array(
      "Reference" => "SEGURO DE VIDA",
      "Amount" => floatval( preg_replace( "/[^0-9]/", "", $cotacao["premio_liquido_total"] ) / 100 )
    ),
    "Transaction" => array(
      "MerchantOrderID" => $cotacao_id,
      "MerchantSoftDescriptor" => $forma["pagamento"][0]["nome_fatura"],
      "EnabledTransactionTypes" => $EnabledTransactionTypes,
      "EnabledDebitBrands" => $EnabledDebitBrands,
      "EnabledCreditBrands" => $EnabledCreditBrands,
      "EnabledInstallments" => $EnabledInstallments,
      ),
    "Acceleration" => array(
      "NPS" => array(
        "Enabled" => false,
        "CampaignID" => 0
      ),
      "CAP" => array(
        "Enabled" => false,
        "SerieID" => 0
      )
    ),
    "Notification" => array(
      "Enabled" => false,
      "Method" => "SMS"
    )
  );

  if( sizeof( $EnabledInstallments ) > 1 && $Installments ) {
    $Payload["Installments"] = $Installments;
  }
  $merchantKey = $this->config->item("Pagmax360_merchantKey");
  $merchantId = $this->config->item("Pagmax360_merchantId");

  $Url = "https://gw.pagmax.com.br/v3/api/checkout";
  $myCurl = curl_init();
  curl_setopt( $myCurl, CURLOPT_URL, $Url );
  curl_setopt( $myCurl, CURLOPT_FRESH_CONNECT, 1 );
  curl_setopt( $myCurl, CURLOPT_POST, 1 );
  curl_setopt( $myCurl, CURLOPT_VERBOSE, 0);
  curl_setopt( $myCurl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt( $myCurl, CURLOPT_HTTPHEADER, array( "Content-Type: application/json", "merchantId: $merchantId", "merchantKey: $merchantKey" ) );
  curl_setopt( $myCurl, CURLOPT_POSTFIELDS, json_encode( $Payload ) );
  curl_setopt( $myCurl, CURLOPT_TIMEOUT, 15 );
  curl_setopt( $myCurl, CURLOPT_CONNECTTIMEOUT, 15 );
  $Response = curl_exec( $myCurl );
  curl_close( $myCurl );
  $x = json_decode( $Response, true );
  $r = get_object_vars( json_decode( $x ) );

  ?>

<div class="row" id="pagamento-boleto">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body no-padding">
        <div class="alert alert-callout alert-danger no-margin">
          <strong class="text-xl"><?php echo $r["Message"] ?></strong><br>
          <?php if( $r["Code"] == 0 ) { ?>
          <span class="text-danger"><?php echo "<a target=\"_blank\" href=\"" . $r["Link"] . "\">" . $r["Link"] . "</a>" ?></span>
          <?php } ?>
          <div class="stick-bottom-left-right">
          </div>
        </div>
      </div><!--end .card-body -->
    </div><!--end .card -->
  </div>
  <?php if( $r["Code"] == 0 ) { ?>
  <div class="col-md-12">
      <button type="button" class="btn btn-info btn-sm push-center" onclick="window.open('<?php echo $r["Link"] ?>','new','width=380,height=640')">
        <i class="fa fa-link"></i>
        ACESSO AO <?php echo $forma["pagamento"][0]["forma_pagamento_nome"] ?>
    </button>
  </div>
  <?php } ?>
</div>




