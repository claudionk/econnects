<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
  /**
  * ZGrid Datatables
  *
  * Biblioteca para Utilziação do datatables.net para for CodeIgniter
  *
  * @package    CodeIgniter
  * @subpackage libraries
  * @category   library
  * @version    1.0 <beta>
  * @author     Danilo Quinelato <danilo62x@gmail.com>
  *
  * @link
  */
  class ZGrid
  {
    /**
    * Global container variables for chained argument results
    *
    */
    private $ci;
    private $table;
    private $distinct;
    private $group_by                   = array();
    private $select                     = array();
    private $joins                      = array();
    private $columns                    = array();
    private $where                      = array();
    private $or_where                   = array();
    private $where_in                   = array();
    private $like                       = array();
    private $or_like                    = array();
    private $filter                     = array();
    private $add_columns                = array();
    private $edit_columns               = array();
    private $unset_columns              = array();
    private $soft_delete                = TRUE;
    private $soft_delete_key            = 'deletado';

    private $grid_id                    = '';
    private $grid_key                   = '';
    private $grid_form_action           = '';
    private $grid_form_method           = 'POST';
    private $grid_form_enctype          = 'multipart/form-data';
    private $grid_columns               = array();
    private $grid_assets                = '';
    private $grid_pages                 = array();
    private $grid_pagination            = TRUE;
    private $grid_checkbox              = TRUE;
    private $grid_buttons               = array();
    private $grid_script                = array();
    private $grid_url_data              = '';
    private $grid_controller            = '';
    private $grid_order                 = array();
    private $grid_actions               = array();
    private $grid_actions_footer        = array();
    private $grid_actions_default       = array();
    private $grid_actions_footer_default= array();
    private $grid_action_add            = TRUE;
    private $grid_action_view           = TRUE;
    private $grid_action_edit           = TRUE;
    private $grid_action_delete         = TRUE;
    private $grid_permissoes            = array();
    private $grid_render                = '';

    /**
    * Cópia da Instancia do CI
    */
    public function __construct()
    {
      $this->ci =& get_instance();

      $this->grid_controller = "/{$this->ci->router->directory}".$this->ci->router->fetch_class() . "/";
      $this->grid_url_data = "/{$this->ci->router->directory}".$this->ci->router->fetch_class() . "/datadb";
      $this->grid_id = 'zGrid';
      $this->grid_assets = app_assets_url('zGrid', 'admin');

      $this->grid_pages = array(20 => 20, 50 => 50, 100 => 100, -1 => 'Todos' );

      $this->grid_buttons = NULL;

      $this->grid_pagination = TRUE;

      $this->grid_order = [1 => 'asc'];


      //actions no grid
      $this->grid_actions_default['view'] = array('title' => 'Visualizar', 'url' => $this->grid_controller ."view/$1", 'html' => '<i class="fa fa-file-o" aria-hidden="true"> Visualizar </i>' , 'class' => 'btn-sm btn-primary');
      $this->grid_actions_default['edit'] = array('title' => 'Ediar', 'url' => $this->grid_controller ."edit/$1", 'html' => '<i class="fa fa-pencil" aria-hidden="true"> Editar </i>', 'class' => 'btn-sm btn-primary');
      $this->grid_actions_default['delete'] = array('title' => 'Excluir', 'url' => $this->grid_controller ."delete/$1", 'html' => '<i class="fa fa-trash-o" aria-hidden="true"> Excluir </i>', 'class' => 'btn-sm btn-danger deleteRowButton');

      //actions no Menu Footer
      $this->grid_actions_footer_default['add']    = array('title' => 'Adicionar',  'url' => $this->grid_controller ."add/", 'html' => '<i class="fa  fa-plus" aria-hidden="true"> Adicionar </i>', 'class' => 'btn  btn-app btn-primary');
      $this->grid_actions_footer_default['delete'] = array('title' => 'Excluir Selecionados',  'url' => "javascript: delete_selected();", 'html' => '<i class="fa fa-trash-o" aria-hidden="true"> Excluir Selecionados </i>', 'class' => 'btn btn-app btn-danger');

    }


    public function grid_config($values = array()){

      foreach ($values as $index => $value) {
        $index = "grid_{$index}";
        if(property_exists($this, $index)){
          $this->$index = $value;
        }
      }

      return $this;

    }

    public function add_grid_order($order = array()){

      $this->grid_order = array();

      foreach ($order as $index => $item) {
        $this->grid_order += [(int)$index => $item];
      }
      return $this;
    }


    public function add_grid_action($name, $title, $url, $class='', $params = array(), $html = '', $type = 'grid'){



      $url = substr($url, -1) != '/' ? $url."/" : $url;
      $i= 1;
      if(is_array($params) && count($params) > 1) {
        foreach ($params as $p) {
          $url .= '$' . $i;
          $i++;
        }
      }else{
        $url .= '$1';
      }

      if($type == 'grid') {
        $this->grid_actions[$name] = array('title' => $title, 'url' => $url, 'html' => $html, 'class' => $class);
      }else{
        $this->grid_actions_footer[$name] = array('title' => $title, 'url' => $url, 'html' => $html, 'class' => $class);
      }

      return $this;

    }

    private function _build_default_actions(){

      if($this->grid_action_view){
        $this->grid_actions['view'] = (isset($this->grid_actions['view'])) ? $this->grid_actions['view'] : $this->grid_actions_default['view'];
      }
      if($this->grid_action_edit){
        $this->grid_actions['edit'] = (isset($this->grid_actions['edit'])) ? $this->grid_actions['edit'] : $this->grid_actions_default['edit'];
      }
      if($this->grid_action_delete){
        $this->grid_actions['delete'] = (isset($this->grid_actions['delete'])) ? $this->grid_actions['delete'] : $this->grid_actions_default['delete'];
      }
      if($this->grid_action_delete && $this->grid_checkbox){
        $this->grid_actions_footer['delete'] = (isset($this->grid_actions_footer['delete'])) ? $this->grid_actions_footer['delete'] : $this->grid_actions_footer_default['delete'];
      }
      if($this->grid_action_add){
        $this->grid_actions_footer['add'] = (isset($this->grid_actions_footer['add'])) ? $this->grid_actions_footer['add'] : $this->grid_actions_footer_default['add'];
      }


    }


    public function render(){

      $script = array();
      $data = array();
      $num_columns = 0;


      $this->_build_default_actions();


      $script[] = " ";
      $script[] = " var __datatable;";
      $script[] = " ";
      $script[] = " $(document).ready(function() {";
      $script[] = "   __datatable = $('#{$this->grid_id}').DataTable( {";
      $script[] = "   'processing': true,";
      $script[] = "   'serverSide': true,";
      $script[] = "   'ajax': {";
      $script[] = "       'url': '{$this->grid_url_data}',";
      $script[] = "       'type': 'POST',";
      $script[] = "   },";
      //language
      $script[] = "   'language': {";
      $script[] = "       'url': '{$this->grid_assets}/json/language.json'";
      $script[] = "   },";

      //Botões
      if(isset($this->grid_buttons) && is_array($this->grid_buttons)) {
        $script[] = "   dom: 'Bfrtip',";

        $buttons = "";
        foreach ($this->grid_buttons as $index => $grid_button) {
          $buttons .= "'{$grid_button}',";
        }
        $script[] = "  buttons: [{$buttons}],";


      }

      //Menu Quantidade de páginas
      $script[] = "   ";
      if(isset($this->grid_pages) && is_array($this->grid_pages)) {
        $lengthMenu = " lengthMenu: [[  ";
        foreach ($this->grid_pages as $index => $grid_page) {
          $lengthMenu .= "{$index},";
        }
        $lengthMenu .= "],[";
        foreach ($this->grid_pages as $index => $grid_page) {
          $lengthMenu .= "'{$grid_page}',";
        }
        $lengthMenu .= " ]],  ";
        $script[] = $lengthMenu;
      }
      $script[] = "   ";
      //Paginação
      if($this->grid_pagination === FALSE) {
        $script[] = "   'paging': false,";
      }

      //checkbox
      /**
       * select: {
      style:    'os',
      selector: 'td:first-child'
      },
       */

      if($this->grid_checkbox === TRUE) {
        $script[] = "   'columnDefs': [{ ";
        $script[] = "                     'targets': 0, ";
        $script[] = "                     'data': null, ";
        $script[] = "                     'defaultContent': '', ";
        $script[] = "                     'className': 'control', ";
        $script[] = "                     'orderable': false, ";
        $script[] = "                     'checkboxes': { ";
        $script[] = "                         'selectRow': true ";
        $script[] = "                          } ";
        $script[] = "                  }], ";
        $script[] = "   select: { ";
        $script[] = "                style:    'multi',";
        //$script[] = "                type:    'api',";
        $script[] = "           }, ";
      }


      if(is_array($this->grid_order) && count($this->grid_order) > 0){
        $script[] = "   order: [";
        foreach ($this->grid_order as $index => $item) {
          $script[] = "  [ {$index}, '{$item}' ], ";
        }
        $script[] = "   ], ";
      }

      $script[] = "   'columns': [";

      /*
      {
        "className": 'details-control',
                        "orderable": false,
                        "data": null,
                        "defaultContent": ''
                    }
      */

      if($this->grid_checkbox === TRUE){
        $num_columns++;
        $script[] = "{ 'data':  null, 'title' : '', 'orderable': false, 'defaultContent': '', 'checkboxes' : {'selectRow': true}, 'searchable' : false},";
       // $script[] = "{ 'data':  null},";
      }
      foreach ($this->grid_columns as $column){
          $num_columns++;
          $script[] = "{ 'data': '{$column['name']}', 'title' : '{$column['label']}'},";
      }

      $script[] = (count($this->grid_actions) > 0) ?  "{ 'data': '__actions', 'title' : 'Ações',  'orderable': false, 'searchable' : false }," : '';
      $script[] = "     ]";

      $script[] = "   } );";
      $script[] = " } );";

      $actions_footer = '';
      if(is_array($this->grid_actions_footer) && count($this->grid_actions_footer)>0) {
        foreach ($this->grid_actions_footer as $index => $grid_action) {
          $actions_footer .= "<a alt=\"{$grid_action['title']}\"  title=\"{$grid_action['title']}\"  class=\"{$grid_action['class']}\" href=\"{$grid_action['url']}\">{$grid_action['html']}</a>&nbsp;";

        }
      }

      $data['form'] = array('action' => $this->grid_form_action, 'method' => $this->grid_form_method, 'enctype' => $this->grid_form_enctype);
      $data['key'] = $this->grid_key;
      $data['grid_id'] = $this->grid_id;
      $data['columns'] = $this->grid_columns;
      $data['num_columns'] = $num_columns;
      $data['actions_footer'] = $actions_footer;
      $data['actions'] = $this->grid_actions;
      $data['config'] = $this->getAllConfigGrid();
      $data['javascript'] = implode("\n", $script);

      return $this->ci->load->view('zGrid/index', array('grid' => $data), TRUE);


    }

    public function getAllConfigGrid()
    {
      $array = array();


      foreach ($this as $key => $value) {
        if ((substr($key, 0, 5) == 'grid_') && property_exists($this, $key)) {
          $array[$key] =  $value;
        }
      }

      return $array;
    }

    /**
     * Adiciona Coluna no Grid
     *
     * @param $column array ('table', 'name', 'label', 'width', 'function')
     * @return mixed
     */

    public function add_grid_column($column){
      $this->grid_columns[] =  $column;
      return $this;
    }




    /**
    * Usado para multiplas base de dados
    */
    public function set_database($db_name)
    {
      $db_data = $this->ci->load->database($db_name, TRUE);
      $this->ci->db = $db_data;
    }


    /**
    * Generates the SELECT portion of the query
    *
    * @param string $columns
    * @param bool $backtick_protect
    * @return mixed
    */
    public function select($columns, $backtick_protect = TRUE)
    {

      foreach($this->explode(',', $columns) as $val)
      {
        $column = trim(preg_replace('/(.*)\s+as\s+(\w*)/i', '$2', $val));
        $column = preg_replace('/.*\.(.*)/i', '$1', $column); // get name after `.`
        $this->columns[] =  $column;
        $this->select[$column] =  trim(preg_replace('/(.*)\s+as\s+(\w*)/i', '$1', $val));
      }

      $this->ci->db->select($columns, $backtick_protect);
      return $this;
    }

    /**
    * Generates the DISTINCT portion of the query
    *
    * @param string $column
    * @return mixed
    */
    public function distinct($column)
    {
      $this->distinct = $column;
      $this->ci->db->distinct($column);
      return $this;
    }

    /**
    * Generates a custom GROUP BY portion of the query
    *
    * @param string $val
    * @return mixed
    */
    public function group_by($val)
    {
      $this->group_by[] = $val;
      $this->ci->db->group_by($val);
      return $this;
    }

    /**
    * Generates the FROM portion of the query
    *
    * @param string $table
    * @return mixed
    */
    public function from($table)
    {
      $this->table = $table;
      return $this;
    }

    /**
    * Generates the JOIN portion of the query
    *
    * @param string $table
    * @param string $fk
    * @param string $type
    * @return mixed
    */
    public function join($table, $fk, $type = NULL)
    {
      $this->joins[] = array($table, $fk, $type);
      $this->ci->db->join($table, $fk, $type);
      return $this;
    }

    /**
    * Generates the WHERE portion of the query
    *
    * @param mixed $key_condition
    * @param string $val
    * @param bool $backtick_protect
    * @return mixed
    */
    public function where($key_condition, $val = NULL, $backtick_protect = TRUE)
    {
      $this->where[] = array($key_condition, $val, $backtick_protect);
      $this->ci->db->where($key_condition, $val, $backtick_protect);
      return $this;
    }

    /**
    * Generates the WHERE portion of the query
    *
    * @param mixed $key_condition
    * @param string $val
    * @param bool $backtick_protect
    * @return mixed
    */
    public function or_where($key_condition, $val = NULL, $backtick_protect = TRUE)
    {
      $this->or_where[] = array($key_condition, $val, $backtick_protect);
      $this->ci->db->or_where($key_condition, $val, $backtick_protect);
      return $this;
    }
    
    /**
    * Generates the WHERE IN portion of the query
    *
    * @param mixed $key_condition
    * @param string $val
    * @param bool $backtick_protect
    * @return mixed
    */
    public function where_in($key_condition, $val = NULL)
    {
      $this->where_in[] = array($key_condition, $val);
      $this->ci->db->where_in($key_condition, $val);
      return $this;
    }

    /**
    * Generates the WHERE portion of the query
    *
    * @param mixed $key_condition
    * @param string $val
    * @param bool $backtick_protect
    * @return mixed
    */
    public function filter($key_condition, $val = NULL, $backtick_protect = TRUE)
    {
      $this->filter[] = array($key_condition, $val, $backtick_protect);
      return $this;
    }

    /**
    * Generates a %LIKE% portion of the query
    *
    * @param mixed $key_condition
    * @param string $val
    * @param bool $backtick_protect
    * @return mixed
    */
    public function like($key_condition, $val = NULL, $side = 'both')
    {
      $this->like[] = array($key_condition, $val, $side);
      $this->ci->db->like($key_condition, $val, $side);
      return $this;
    }

    /**
    * Generates the OR %LIKE% portion of the query
    *
    * @param mixed $key_condition
    * @param string $val
    * @param bool $backtick_protect
    * @return mixed
    */
    public function or_like($key_condition, $val = NULL, $side = 'both')
    {
      $this->or_like[] = array($key_condition, $val, $side);
      $this->ci->db->or_like($key_condition, $val, $side);
      return $this;
    }

    /**
    * Sets additional column variables for adding custom columns
    *
    * @param string $column
    * @param string $content
    * @param string $match_replacement
    * @return mixed
    */
    public function add_column($column, $content, $match_replacement = NULL)
    {
      $this->add_columns[$column] = array('content' => $content, 'replacement' => $this->explode(',', $match_replacement));
      return $this;
    }

    /**
    * Sets additional column variables for editing columns
    *
    * @param string $column
    * @param string $content
    * @param string $match_replacement
    * @return mixed
    */
    public function edit_column($column, $content, $match_replacement)
    {
      $this->edit_columns[$column][] = array('content' => $content, 'replacement' => $this->explode(',', $match_replacement));
      return $this;
    }

    /**
    * Unset column
    *
    * @param string $column
    * @return mixed
    */
    public function unset_column($column)
    {
      $column=explode(',',$column);
      $this->unset_columns=array_merge($this->unset_columns,$column);
      return $this;
    }

    /**
    * Builds all the necessary query segments and performs the main query based on results set from chained statements
    *
    * @param string $output
    * @param string $charset
    * @return string
    */
    public function generate($output = 'json', $charset = 'UTF-8')
    {
      if(strtolower($output) == 'json')
        $this->get_paging();

      $this->get_ordering();
      $this->get_filtering();

      $this->_build_default_actions();
      if(count($this->grid_actions) > 0){
          $acoes = '';
          foreach ($this->grid_actions as $index => $grid_action) {
            $acoes .= "<a alt=\"{$grid_action['title']}\"  title=\"{$grid_action['title']}\"  class=\"{$grid_action['class']}\" href=\"{$grid_action['url']}\">{$grid_action['html']}</a>&nbsp;";
          
          }
        $this->add_column('__actions', $acoes, $this->grid_key);
      }
//
      return $this->produce_output(strtolower($output), strtolower($charset));
    }

    /**
    * Generates the LIMIT portion of the query
    *
    * @return mixed
    */
    private function get_paging()
    {
      $iStart = $this->ci->input->post('start');
      $iLength = $this->ci->input->post('length');

      if($iLength != '' && $iLength != '-1')
        $this->ci->db->limit($iLength, ($iStart)? $iStart : 0);
    }

    /**
    * Generates the ORDER BY portion of the query
    *
    * @return mixed
    */
    private function get_ordering()
    {

      $Data = $this->ci->input->post('columns');


      if ($this->ci->input->post('order'))
        foreach ($this->ci->input->post('order') as $key)
          if($this->check_cType())
            $this->ci->db->order_by($Data[$key['column']]['data'], $key['dir']);
          else
            $this->ci->db->order_by($this->columns[$key['column']] , $key['dir']);

    }

    /**
    * Generates a %LIKE% portion of the query
    *
    * @return mixed
    */
    private function get_filtering()
    {

      $mColArray = $this->ci->input->post('columns');

      $sWhere = '';
      $search = $this->ci->input->post('search');
      $sSearch = $this->ci->db->escape_like_str(trim($search['value']));
      $columns = array_values(array_diff($this->columns, $this->unset_columns));

      //print_r($mColArray);
      if($sSearch != '')
        for($i = 0; $i < count($mColArray); $i++)
          if ($mColArray[$i]['searchable'] == 'true' && !array_key_exists($mColArray[$i]['data'], $this->add_columns))
            if($this->check_cType())
              $sWhere .= $this->select[$mColArray[$i]['data']] . " LIKE '%" . $sSearch . "%' OR ";
            else
              $sWhere .= $this->select[$this->columns[$i]] . " LIKE '%" . $sSearch . "%' OR ";


      $sWhere = substr_replace($sWhere, '', -3);

      if($sWhere != '')
        $this->ci->db->where('(' . $sWhere . ')');

      // TODO : sRangeSeparator

      foreach($this->filter as $val)
        $this->ci->db->where($val[0], $val[1], $val[2]);

      if($this->soft_delete){
        $this->ci->db->where("{$this->table}.{$this->soft_delete_key}", FALSE);
      }
    }

    /**
    * Compiles the select statement based on the other functions called and runs the query
    *
    * @return mixed
    */
    private function get_display_result()
    {
      return $this->ci->db->get($this->table);
    }

    /**
    * Builds an encoded string data. Returns JSON by default, and an array of aaData if output is set to raw.
    *
    * @param string $output
    * @param string $charset
    * @return mixed
    */
    private function produce_output($output, $charset)
    {
      $aaData = array();
      $rResult = $this->get_display_result();

      if($output == 'json')
      {
        $iTotal = $this->get_total_results();
        $iFilteredTotal = $this->get_total_results(TRUE);
      }

      foreach($rResult->result_array() as $row_key => $row_val)
      {
        $aaData[$row_key] =  ($this->check_cType())? $row_val : array_values($row_val);

        foreach($this->add_columns as $field => $val)
         if($this->check_cType())
            $aaData[$row_key][$field] = $this->exec_replace($val, $aaData[$row_key]);
          else
            $aaData[$row_key][] = $this->exec_replace($val, $aaData[$row_key]);


        foreach($this->edit_columns as $modkey => $modval)
          foreach($modval as $val)
            $aaData[$row_key][($this->check_cType())? $modkey : array_search($modkey, $this->columns)] = $this->exec_replace($val, $aaData[$row_key]);

        $aaData[$row_key] = array_diff_key($aaData[$row_key], ($this->check_cType())? $this->unset_columns : array_intersect($this->columns, $this->unset_columns));

        if(!$this->check_cType())
          $aaData[$row_key] = array_values($aaData[$row_key]);

      }

      if($output == 'json')
      {
        $sOutput = array
        (
          'draw'                => intval($this->ci->input->post('draw')),
          'recordsTotal'        => $iTotal,
          'recordsFiltered'     => $iFilteredTotal,
          'data'                => $aaData
        );

        if($charset == 'utf-8')
          return json_encode($sOutput);
        else
          return $this->jsonify($sOutput);
      }
      else
        return array('aaData' => $aaData);
    }

    /**
    * Get result count
    *
    * @return integer
    */
    private function get_total_results($filtering = FALSE)
    {
      if($filtering)
        $this->get_filtering();

      foreach($this->joins as $val)
        $this->ci->db->join($val[0], $val[1], $val[2]);

      foreach($this->where as $val)
        $this->ci->db->where($val[0], $val[1], $val[2]);

      foreach($this->or_where as $val)
        $this->ci->db->or_where($val[0], $val[1], $val[2]);
        
      foreach($this->where_in as $val)
        $this->ci->db->where_in($val[0], $val[1]);

      foreach($this->group_by as $val)
        $this->ci->db->group_by($val);

      foreach($this->like as $val)
        $this->ci->db->like($val[0], $val[1], $val[2]);

      foreach($this->or_like as $val)
        $this->ci->db->or_like($val[0], $val[1], $val[2]);

      if(strlen($this->distinct) > 0)
      {
        $this->ci->db->distinct($this->distinct);
        $this->ci->db->select($this->columns);
      }

      if($this->soft_delete){
        $this->ci->db->where("{$this->table}.{$this->soft_delete_key}", FALSE);
      }

      $query = $this->ci->db->get($this->table, NULL, NULL, FALSE);
      return $query->num_rows();
    }

    /**
    * Runs callback functions and makes replacements
    *
    * @param mixed $custom_val
    * @param mixed $row_data
    * @return string $custom_val['content']
    */
    private function exec_replace($custom_val, $row_data)
    {
      $replace_string = '';
      
      // Go through our array backwards, else $1 (foo) will replace $11, $12 etc with foo1, foo2 etc
      $custom_val['replacement'] = array_reverse($custom_val['replacement'], true);

      if(isset($custom_val['replacement']) && is_array($custom_val['replacement']))
      {
        //Added this line because when the replacement has over 10 elements replaced the variable "$1" first by the "$10"
        $custom_val['replacement'] = array_reverse($custom_val['replacement'], true);
        foreach($custom_val['replacement'] as $key => $val)
        {
          $sval = preg_replace("/(?<!\w)([\'\"])(.*)\\1(?!\w)/i", '$2', trim($val));

      if(preg_match('/(\w+::\w+|\w+)\((.*)\)/i', $val, $matches) && is_callable($matches[1]))
          {
            $func = $matches[1];
            $args = preg_split("/[\s,]*\\\"([^\\\"]+)\\\"[\s,]*|" . "[\s,]*'([^']+)'[\s,]*|" . "[,]+/", $matches[2], 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

            foreach($args as $args_key => $args_val)
            {
              $args_val = preg_replace("/(?<!\w)([\'\"])(.*)\\1(?!\w)/i", '$2', trim($args_val));
              $args[$args_key] = (in_array($args_val, $this->columns))? ($row_data[($this->check_cType())? $args_val : array_search($args_val, $this->columns)]) : $args_val;
            }

            $replace_string = call_user_func_array($func, $args);
          }
          elseif(in_array($sval, $this->columns))
            $replace_string = $row_data[($this->check_cType())? $sval : array_search($sval, $this->columns)];
          else
            $replace_string = $sval;

          $custom_val['content'] = str_ireplace('$' . ($key + 1), $replace_string, $custom_val['content']);
        }
      }

      return $custom_val['content'];
    }

    /**
    * Check column type -numeric or column name
    *
    * @return bool
    */
    private function check_cType()
    {
      $column = $this->ci->input->post('columns');
      if(is_numeric($column[0]['data']))
        return FALSE;
      else
        return TRUE;
    }


    /**
    * Return the difference of open and close characters
    *
    * @param string $str
    * @param string $open
    * @param string $close
    * @return string $retval
    */
    private function balanceChars($str, $open, $close)
    {
      $openCount = substr_count($str, $open);
      $closeCount = substr_count($str, $close);
      $retval = $openCount - $closeCount;
      return $retval;
    }

    /**
    * Explode, but ignore delimiter until closing characters are found
    *
    * @param string $delimiter
    * @param string $str
    * @param string $open
    * @param string $close
    * @return mixed $retval
    */
    private function explode($delimiter, $str, $open = '(', $close=')')
    {
      $retval = array();
      $hold = array();
      $balance = 0;
      $parts = explode($delimiter, $str);

      foreach($parts as $part)
      {
        $hold[] = $part;
        $balance += $this->balanceChars($part, $open, $close);

        if($balance < 1)
        {
          $retval[] = implode($delimiter, $hold);
          $hold = array();
          $balance = 0;
        }
      }

      if(count($hold) > 0)
        $retval[] = implode($delimiter, $hold);

      return $retval;
    }

    /**
    * Workaround for json_encode's UTF-8 encoding if a different charset needs to be used
    *
    * @param mixed $result
    * @return string
    */
    private function jsonify($result = FALSE)
    {
      if(is_null($result))
        return 'null';

      if($result === FALSE)
        return 'false';

      if($result === TRUE)
        return 'true';

      if(is_scalar($result))
      {
        if(is_float($result))
          return floatval(str_replace(',', '.', strval($result)));

        if(is_string($result))
        {
          static $jsonReplaces = array(array('\\', '/', '\n', '\t', '\r', '\b', '\f', '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
          return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $result) . '"';
        }
        else
          return $result;
      }

      $isList = TRUE;

      for($i = 0, reset($result); $i < count($result); $i++, next($result))
      {
        if(key($result) !== $i)
        {
          $isList = FALSE;
          break;
        }
      }

      $json = array();

      if($isList)
      {
        foreach($result as $value)
          $json[] = $this->jsonify($value);

        return '[' . join(',', $json) . ']';
      }
      else
      {
        foreach($result as $key => $value)
          $json[] = $this->jsonify($key) . ':' . $this->jsonify($value);

        return '{' . join(',', $json) . '}';
      }
    }
	
	 /**
     * returns the sql statement of the last query run
     * @return type
     */
    public function last_query()
    {
      return  $this->ci->db->last_query();
    }
  }
/* End of file Datatables.php */
/* Location: ./application/libraries/Datatables.php */
