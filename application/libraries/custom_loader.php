<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by Zazz Tecnologia da Informação
 * User: Danilo Quinelato
 * Date: 15/07/2016
 * Time: 17:40
 */

class Custom_loader  extends CI_Loader {
    public function __construct() {
        parent::__construct();
    }

    public function unload_library($name) {
        if (count($this->_ci_classes)) {
            foreach ($this->_ci_classes as $key => $value) {
                if ($key == $name) {
                    unset($this->_ci_classes[$key]);
                }
            }
        }

        if (count($this->_ci_loaded_files)) {
            foreach ($this->_ci_loaded_files as $key => $value)
            {
                $segments = explode("/", $value);
                if (strtolower($segments[sizeof($segments) - 1]) == $name.".php") {
                    unset($this->_ci_loaded_files[$key]);
                }
            }
        }

        $CI =& get_instance();
        $name = ($name != "user_agent") ? $name : "agent";
        unset($CI->$name);
    }
}