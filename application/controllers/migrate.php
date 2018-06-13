<?php
/**
 * Created by PhpStorm.
 * User: danil
 * Date: 29/08/2016
 * Time: 12:08
 */
class migrate extends CI_Controller {
    public function index()
    {
        // load migration library
        $this->load->library('migration');

        if ( ! $this->migration->current())
        {
            echo 'Error' . $this->migration->error_string();
        } else {
            echo 'Migrations ran successfully!';
        }
    }
}