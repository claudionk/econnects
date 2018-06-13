<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Template {
    /*
     * Global template data
     */
    private $_template_data = array();

    private $_ci;

    private $_layout = false;

    private $css_list = array();

    private $js_list = array();

    private $js_inline_list = array();
    private $js_inline_append_list = array();
    private $js_inline_prepend_list = array();

    private $js_inline_block_list = array();
    private $js_inline_block_append_list = array();
    private $js_inline_block_prepend_list = array();


    private $css_inline_list = array();


    private $_breadcrumbs = array();


    function __construct($config = array())
    {
        $this->_ci =& get_instance();

        log_message('debug', 'Template Class Initialized');
    }

    public function set($name, $value)
    {
        $this->_template_data[$name] = $value;
    }
    public function is_set($name)
    {
        return isset($this->_template_data[$name]);
    }

    public function js($item){

        $this->js_list[] = $item;
        return $this;
    }
    public function js_inline($string){

        $this->js_inline_list[] = $string;
    }
    public function js_inline_append($string){

        $this->js_inline_append_list[] = $string;
    }
    public function js_inline_prepend($string){

        $this->js_inline_prepend_list[] = $string;
    }

    public function js_inline_block($block, $string){

        $this->js_inline_block_list[$block][] = $string;
    }

    public function js_inline_block_append($block, $string){

        $this->js_inline_block_append_list[$block][] = $string;
    }
    public function js_inline_block_prepend($block, $string){

        $this->js_inline_block_prepend_list[$block][] = $string;
    }



    public function css_inline($string){

        $this->css_inline_list[] = $string;
    }
    public function css($item)
    {
        if(!in_array($item, $this->css_list))
        {
            $this->css_list[] = $item;
        }

        return $this;

    }
    public function meta($name, $content){



    }

    public function get($name)
    {
        if($this->is_set($name)){
            return $this->_template_data[$name];
        }

        return null;
    }

    public function set_layout($view)
    {
        $this->_layout = $view;

        return $this;
    }

    public function set_breadcrumb($name, $uri = '', $icon = '')
    {
        $this->_breadcrumbs[] = array('name' => $name, 'uri' => $uri, 'icon' => $icon  );
        return $this;
    }


    function load($layout = '', $view = '' , $view_data = array(), $return = FALSE)
    {
        $view_data['breadcrumbs'] = $this->_breadcrumbs;

        $view_data['js_for_layout'] = '';
        foreach ($this->js_list as $v)
            $view_data['js_for_layout'] .= sprintf("<script type=\"text/javascript\" src=\"%s\"></script>\n", $v);

        $view_data['css_for_layout'] = '';


        foreach ($this->css_list as $v)
            $view_data['css_for_layout'] .= sprintf("<link rel=\"stylesheet\" type=\"text/css\"  href=\"%s\" />\n", $v);

        $view_data['js_inline_for_layout'] = '';

        if($this->js_inline_list || $this->js_inline_block_list ){
            $js_content = "<script type=\"text/javascript\">\n";

            foreach($this->js_inline_prepend_list as $prepend){

                $js_content .= $prepend . "\n";
            }


            foreach($this->js_inline_block_list as $block => $block_items){

                if($this->js_inline_block_prepend_list[$block]){
                    foreach($this->js_inline_block_prepend_list[$block] as $block_prepend){

                        $js_content .= $block_prepend . "\n";
                    }
                }
                foreach($block_items as $block_item){

                    $js_content .= $block_item . "\n";
                }


                if($this->js_inline_block_append_list[$block]){
                    foreach($this->js_inline_block_append_list[$block] as $block_append){

                        $js_content .= $block_append . "\n";
                    }
                }


            }
            foreach ($this->js_inline_list as $inline){

                $js_content .= $inline . "\n";

            }

            foreach($this->js_inline_append_list as $append){

                $js_content .= $append . "\n";
            }
            $js_content .= "</script>\n";
            $view_data['js_inline_for_layout'] = $js_content;
        }

        $view_data = array_merge($this->_template_data, $view_data);

        $this->set('contents', $this->_ci->load->view($view, $view_data, TRUE));

        log_message('debug', 'Load Template: ' . $layout . ' Content View: ' . $view );

        return $this->_ci->load->view($layout, $this->_template_data, $return);


    }

}

