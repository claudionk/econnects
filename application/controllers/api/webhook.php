<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Apolice
 */
class Webhook extends CI_Controller {
  public $api_key;
  public $http_method;
  
  public function __construct() {
    parent::__construct();
  }
  
  public function index() {
    $this->http_method = $_SERVER["REQUEST_METHOD"];
    if( $_SERVER["REQUEST_METHOD"] === "GET" ) {
      $GET = $_GET;
      $x = $this->checkout_pagmax( $GET );
    } else {
      if( $_SERVER["REQUEST_METHOD"] === "POST" ) {
        $POST = json_decode( file_get_contents( "php://input" ), true );
        $x = $this->checkout_pagmax( $POST );
      } else {
        die( json_encode( array( "status" => false, "message" => "Invalid HTTP method" ) ) );
      }
    }
  }
  
  private function checkout_pagmax( $REQUEST ) {
    die( json_encode( array( "status" => true, "message" => "{$this->http_method}", "parameters" => $REQUEST ) ) );
  }
  
}
?>


