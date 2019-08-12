<?php
/**
 * A base model with a series of CRUD functions (powered by CI's query builder),
 * validation-in-model support, event callbacks and more.
 *
 * @link http://github.com/jamierumbelow/codeigniter-base-model
 * @copyright Copyright (c) 2012, Jamie Rumbelow <http://jamierumbelow.net>
 */

class MY_Model extends CI_Model
{

    /* --------------------------------------------------------------
     * VARIABLES
     * ------------------------------------------------------------ */

    /**
     * Indicates whether it's a MongoDB model or not.
     */
    protected $_mongodb;

    protected $upload_path = "arquivos";
    /**
     * This model's default database table. Automatically
     * guessed by pluralising the model name.
     */
    protected $_table;

    /**
     * The database connection object. Will be set to the default
     * connection. This allows individual models to use different DBs
     * without overwriting CI's global $this->db connection.
     */
    public $_database;

    /**
     * This model's default primary key or unique identifier.
     * Used by the get(), update() and delete() functions.
     */
    protected $primary_key = 'id';

    /**
     * Support for soft deletes and this model's 'deleted' key
     */
    protected $soft_delete = FALSE;
    protected $soft_delete_key = 'deleted';
    protected $_temporary_with_deleted = FALSE;
    protected $_temporary_only_deleted = FALSE;

    /**
     * The various callbacks available to the model. Each are
     * simple lists of method names (methods will be run on $this).
     */
    protected $before_create = array( 'create_timestamps', 'to_uppercase', 'to_lowercase' );
    protected $after_create = array();
    protected $before_update = array('update_timestamps', 'to_uppercase', 'to_lowercase');
    protected $after_update = array();
    protected $before_get = array();
    protected $after_get = array();
    protected $before_delete = array();
    protected $after_delete = array();

    protected $callback_parameters = array();

    /**
     * Protected, non-modifiable attributes
     */
    protected $protected_attributes = array();

    /**
     * Relationship arrays. Use flat strings for defaults or string
     * => array to customise the class name and primary key
     */
    protected $belongs_to = array();
    protected $has_many = array();

    protected $_with = array();

    /**
     * An array of validation rules. This needs to be the same format
     * as validation rules passed to the Form_validation library.
     */
    protected $validate = array();

    protected $validate_groups = array();


    protected $enable_log = TRUE;
    public $fields = array();


    /**
     *  Example array
     *
     *  public $validate = array(
            array(
                'field' => 'email',
                'label' => 'email',
                'rules' => 'required|valid_email|is_unique[users.email]',
                'groups' => 'default, backend, frontend'),
            array(
                    'field' => 'password',
                    'label' => 'password',
                    'rules' => 'required'
                ),
            array(
                'field' => 'password_confirmation',
                'label' => 'confirm password',
                'rules' => 'required|matches[password]'
            )
        );
     */

    protected $update_at_key = 'update_at';
    protected $create_at_key = 'create_at';

    protected $fields_lowercase = array();
    protected $fields_uppercase = array();




    /**
     * Optionally skip the validation. Used in conjunction with
     * skip_validation() to skip data validation for any future calls.
     */
    protected $skip_validation = FALSE;

    /**
     * By default we return our results as objects. If we need to override
     * this, we can, or, we could use the `as_array()` and `as_object()` scopes.
     */
    protected $return_type = 'object';
    protected $_temporary_return_type = NULL;


    /**
     * Contador foreign
     * @var int
     */
    protected $obj_count_foreign = 0; //Contador

    /* --------------------------------------------------------------
     * GENERIC METHODS
     * ------------------------------------------------------------ */

    /**
     * Initialise the model, tie into the CodeIgniter superobject and
     * try our best to guess the table name.
     */
    public function __construct()
    {
        parent::__construct();


        if ($this->_mongodb)
        {
            // Load MongoDB library
            $this->load->library('cimongo/cimongo');
            // Set interface object name
            $this->_database = $this->cimongo;
        }else{

            $this->load->helper('inflector');

            $this->_fetch_table();

            $this->_build_validation_groups();

            $this->_database = $this->db;

            array_unshift($this->before_create, 'protect_attributes');
            array_unshift($this->before_update, 'protect_attributes');
        }
            $this->_temporary_return_type = $this->return_type;
    }


    public function build_action_grid(){
        $action = $this->input->post('__action');
        $selected = $this->input->post('selected');


        if($action == 'delete'){
            foreach ($selected as $index => $item) {
                $this->current_model->delete($item);
            }
            $this->session->set_flashdata('succ_msg', 'Registro(s) excluido(s) corretamente.');
        }
    }


    /* --------------------------------------------------------------
     * CRUD INTERFACE
     * ------------------------------------------------------------ */

    /**
     * Fetch a single record based on the primary key. Returns an object.
     */
    public function get($primary_value)
    {
        //Seleciona tudo
        $this->_database->select("{$this->_table}.*");

        if ($this->_mongodb)
        {
            $result = $this->_database->get_where("{$this->_table}", array('_id' =>  new MongoId($primary_value)))->row_array();
            return $result;
        }
        else
        {
            return $this->get_by("{$this->_table}.{$this->primary_key}", $primary_value);
        }
    }


    /**
     * Fetch a single record based on an arbitrary WHERE call. Can be
     * any valid value to $this->_database->where().
     */
    public function get_by()
    {

        $this->_database->select($this->_table . '.*');
        $where = func_get_args();

        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where("{$this->_table}.{$this->soft_delete_key}", (bool)$this->_temporary_only_deleted);
        }

        $this->_set_where($where);

        $this->trigger('before_get');

        $row = $this->_database->get($this->_table)
            ->{$this->_return_type()}();
        $this->_temporary_return_type = $this->return_type;

        $row = $this->trigger('after_get', $row);

        $this->_with = array();
        return $row;
    }

    /**
     * Fetch an array of records based on an array of primary values.
     */
    public function get_many($values)
    {
        $this->_database->where_in($this->primary_key, $values);

        return $this->get_all();
    }

    /**
     * Fetch an array of records based on an arbitrary WHERE call.
     */
    public function get_many_by()
    {
        $where = func_get_args();


        $this->_set_where($where);


        return $this->get_all();
    }

    /**
     * Fetch all the records in the table. Can be used as a generic call
     * to $this->_database->get() with scoped methods.
     */
    public function get_all($limit = 0, $offset = 0, $viewAll = true)
    {
        $this->trigger('before_get');


        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where("{$this->_table}.{$this->soft_delete_key}", (bool)$this->_temporary_only_deleted);
        }

        if ($viewAll) {
            $this->_database->select($this->_table . '.*');
        }

        if ($this->_mongodb)
        {
            $result = $this->_database->get($this->_table, $limit, $offset)
                ->{$this->_return_type(1)}();
        }else{
            $result = $this->_database->get($this->_table)
                ->{$this->_return_type(1)}();
        }
        $this->_temporary_return_type = $this->return_type;

        foreach ($result as $key => &$row)
        {
            $row = $this->trigger('after_get', $row, ($key == count($result) - 1));
        }

        $this->_with = array();
        return $result;
    }

    /**
     * Fetch all the records in the table. Can be used as a generic call
     * to $this->_database->get() with scoped methods.
     */
    public function get_all_select($limit = 0, $offset = 0)
    {
        $this->trigger('before_get');


        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where("{$this->_table}.{$this->soft_delete_key}", (bool)$this->_temporary_only_deleted);
        }



        if ($this->_mongodb)
        {
            $result = $this->_database->get($this->_table, $limit, $offset)
                ->{$this->_return_type(1)}();
        }else{
            $result = $this->_database->get($this->_table)
                ->{$this->_return_type(1)}();
        }
        $this->_temporary_return_type = $this->return_type;

        foreach ($result as $key => &$row)
        {
            $row = $this->trigger('after_get', $row, ($key == count($result) - 1));
        }

        $this->_with = array();
        return $result;
    }

    /**
     * Insert a new row into the table. $data should be an associative array
     * of data to be inserted. Returns newly created ID.
     */
    public function insert($data, $skip_validation = FALSE)
    {
        if ($skip_validation === FALSE)
        {
            $data = $this->validate($data);
        }
        if ($data !== FALSE)
        {
            $data = $this->trigger('before_create', $data);

            $this->_database->insert($this->_table, $data);
            $insert_id = $this->_database->insert_id();
            /**
             * Log event insert
             */
            if($this->enable_log){
                $this->log_evento->log_inserir($this , $insert_id, $data);
            }

            $this->trigger('after_create', $insert_id);

            return $insert_id;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Insert multiple rows into the table. Returns an array of multiple IDs.
     */
    public function insert_many($data, $skip_validation = FALSE)
    {
        $ids = array();

        foreach ($data as $key => $row)
        {
            $ids[] = $this->insert($row, $skip_validation, ($key == count($data) - 1));
        }

        /**
         * Log event insert
         */
        foreach($ids as $id){

            if($this->enable_log){
                $this->log_evento->log_inserir($this , $id, $data);
            }

        }

        return $ids;
    }

    /**
     * Updated a record based on the primary value.
     */
    public function update($primary_value, $data, $skip_validation = FALSE)
    {

        $data = $this->trigger('before_update', $data);


        if ($skip_validation === FALSE)
        {
            $data = $this->validate($data);
        }

        if ($data !== FALSE)
        {

            /**
             * Log update
             */
            if($this->enable_log){
                $this->log_evento->log_alterar($this , $this->get($primary_value), $data);
            }


            $result = $this->_database->where($this->primary_key, $primary_value)
                ->set($data)
                ->update($this->_table);


            $this->trigger('after_update', array($data, $result));

            return $result;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Update many records, based on an array of primary values.
     */
    public function update_many($primary_values, $data, $skip_validation = FALSE)
    {
        $data = $this->trigger('before_update', $data);

        if ($skip_validation === FALSE)
        {
            $data = $this->validate($data);
        }

        if ($data !== FALSE)
        {

            /**
             * Log update
             */
            if($this->enable_log){
                foreach($primary_values as $primary_id){

                    $this->log_evento->log_alterar($this , $this->get($primary_id), $data);
                }
            }

            $result = $this->_database->where_in($this->primary_key, $primary_values)
                ->set($data)
                ->update($this->_table);



            $this->trigger('after_update', array($data, $result));

            return $result;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Updated a record based on an arbitrary WHERE clause.
     */
    public function update_by()
    {
        $args = func_get_args();
        $data = array_pop($args);

        $data = $this->trigger('before_update', $data);

     //   if ($this->validate($data) !== FALSE)
     //   {
            $this->_set_where($args);
            $result = $this->_database->set($data)
                ->update($this->_table);
            $this->trigger('after_update', array($data, $result));

            return $result;
      //  }
      //  else
       // {
       //     return FALSE;
       // }
    }

    /**
     * Update all records
     */
    public function update_all($data)
    {
        $data = $this->trigger('before_update', $data);
        $result = $this->_database->set($data)
            ->update($this->_table);
        $this->trigger('after_update', array($data, $result));

        return $result;
    }

    /**
     * Delete a row from the table by the primary value
     */
    public function delete($id)
    {
        $this->trigger('before_delete', $id);
        $this->_database->where($this->primary_key, $id);
        /**
         * log delete event
         */
        if($this->enable_log){
            $this->log_evento->log_excluir($this , $id, $this->get($id));
        }
        if ($this->soft_delete)
        {
            $result = $this->_database->where($this->primary_key, $id)
                           ->update($this->_table, array( $this->soft_delete_key => TRUE ));

        }
        else
        {
            $result = $this->_database->where($this->primary_key, $id)
                           ->delete($this->_table);
        }

        $this->trigger('after_delete', $result);

        return $result;
    }

    /**
     * Delete a row from the database table by an arbitrary WHERE clause
     */
    public function delete_by()
    {
        $where = func_get_args();


        $where = $this->trigger('before_delete', $where);
        if($where){
            $this->_set_where($where);
        }


        if ($this->soft_delete)
        {
            $result = $this->_database->update($this->_table, array( $this->soft_delete_key => TRUE ));
        }
        else
        {
            $result = $this->_database->delete($this->_table);
        }

        $this->trigger('after_delete', $result);

        return $result;
    }

    /**
     * Delete many rows from the database table by multiple primary values
     */
    public function delete_many($primary_values)
    {
        $primary_values = $this->trigger('before_delete', $primary_values);

        /**
         * log delete event
         */
        foreach($primary_values as $primary_id){

            $this->log_evento->log_excluir($this , $primary_id, $this->get($primary_id));
        }

        $this->_database->where_in($this->primary_key, $primary_values);

        if ($this->soft_delete)
        {
            $result = $this->_database->update($this->_table, array( $this->soft_delete_key => TRUE ));
        }
        else
        {
            $result = $this->_database->delete($this->_table);
        }

        $this->trigger('after_delete', $result);

        return $result;
    }


    /**
     * Truncates the table
     */
    public function truncate()
    {
        $result = $this->_database->truncate($this->_table);

        return $result;
    }

    /* --------------------------------------------------------------
     * RELATIONSHIPS
     * ------------------------------------------------------------ */

    public function with($relationship)
    {
        $this->_with[] = $relationship;

        if (!in_array('relate', $this->after_get))
        {
            $this->after_get[] = 'relate';
        }

        return $this;
    }

    public function relate($row)
    {
        if (empty($row))
        {
            return $row;
        }

        foreach ($this->belongs_to as $key => $value)
        {
            if (is_string($value))
            {
                $relationship = $value;
                $options = array( 'primary_key' => $value . '_id', 'model' => $value . '_model' );
            }
            else
            {
                $relationship = $key;
                $options = $value;
            }

            if (in_array($relationship, $this->_with))
            {
                $this->load->model($options['model'], $relationship . '_model');

                if (is_object($row))
                {
                    $row->{$relationship} = $this->{$relationship . '_model'}->get($row->{$options['primary_key']});
                }
                else
                {
                    $row[$relationship] = $this->{$relationship . '_model'}->get($row[$options['primary_key']]);
                }
            }
        }

        foreach ($this->has_many as $key => $value)
        {
            if (is_string($value))
            {
                $relationship = $value;
                $options = array( 'primary_key' => singular($this->_table) . '_id', 'model' => singular($value) . '_model' );
            }
            else
            {
                $relationship = $key;
                $options = $value;
            }

            if (in_array($relationship, $this->_with))
            {
                $this->load->model($options['model'], $relationship . '_model');

                if (is_object($row))
                {
                    $row->{$relationship} = $this->{$relationship . '_model'}->get_many_by($options['primary_key'], $row->{$this->primary_key});
                }
                else
                {
                    $row[$relationship] = $this->{$relationship . '_model'}->get_many_by($options['primary_key'], $row[$this->primary_key]);
                }
            }
        }

        return $row;
    }

    /* --------------------------------------------------------------
     * UTILITY METHODS
     * ------------------------------------------------------------ */

    /**
     * Retrieve and generate a form_dropdown friendly array
     */
    function dropdown()
    {
        $args = func_get_args();

        if(count($args) == 2)
        {
            list($key, $value) = $args;
        }
        else
        {
            $key = $this->primary_key;
            $value = $args[0];
        }

        $this->trigger('before_dropdown', array( $key, $value ));

        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where("{$this->_table}.{$this->soft_delete_key}", FALSE);
        }

        $result = $this->_database->select(array($key, $value))
            ->get($this->_table)
            ->result();

        $options = array();

        foreach ($result as $row)
        {
            $options[$row->{$key}] = $row->{$value};
        }

        $options = $this->trigger('after_dropdown', $options);

        return $options;
    }

    /**
     * Fetch a count of rows based on an arbitrary WHERE call.
     */
    public function count_by()
    {
        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where("{$this->_table}.{$this->soft_delete_key}", (bool)$this->_temporary_only_deleted);
        }

        $where = func_get_args();
        $this->_set_where($where);

        return $this->_database->count_all_results($this->_table);
    }

    /**
     * Fetch a total count of rows, disregarding any previous conditions
     */
    public function count_all()
    {
        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where("{$this->_table}.{$this->soft_delete_key}", (bool)$this->_temporary_only_deleted);
        }

        return $this->_database->count_all($this->_table);
    }


    function get_total()
    {
        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where("{$this->_table}.{$this->soft_delete_key}", (bool)$this->_temporary_only_deleted);
        }
        $result =  $this->_database->count_all_results($this->_table);


        return $result;

    }
    function getTotalCondicao($campo, $valor)
    {
        if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
        {
            $this->_database->where("{$this->_table}.{$this->soft_delete_key}", (bool)$this->_temporary_only_deleted);
            $this->_database->where("{$this->_table}.{$campo}", $valor);
        }
        $result =  $this->_database->count_all_results($this->_table);


        return $result;

    }


    /**
     * Tell the class to skip the insert validation
     */
    public function skip_validation()
    {
        $this->skip_validation = TRUE;
        return $this;
    }

    /**
     * Get the skip validation status
     */
    public function get_skip_validation()
    {
        return $this->skip_validation;
    }

    /**
     * Return the next auto increment of the table. Only tested on MySQL.
     */
    public function get_next_id()
    {
        return (int) $this->_database->select('AUTO_INCREMENT')
            ->from('information_schema.TABLES')
            ->where('TABLE_NAME', $this->_table)
            ->where('TABLE_SCHEMA', $this->_database->database)->get()->row()->AUTO_INCREMENT;
    }

    /**
     * Getter for the table name
     */
    public function table()
    {
        return $this->_table;
    }

    /* --------------------------------------------------------------
     * GLOBAL SCOPES
     * ------------------------------------------------------------ */

    /**
     * Return the next call as an array rather than an object
     */
    public function as_array()
    {
        $this->_temporary_return_type = 'array';
        return $this;
    }

    /**
     * Return the next call as an object rather than an array
     */
    public function as_object()
    {
        $this->_temporary_return_type = 'object';
        return $this;
    }

    /**
     * Don't care about soft deleted rows on the next call
     */
    public function with_deleted()
    {
        $this->_temporary_with_deleted = TRUE;
        return $this;
    }

    /**
     * Only get deleted rows on the next call
     */
    public function only_deleted()
    {
        $this->_temporary_only_deleted = TRUE;
        return $this;
    }

    /* --------------------------------------------------------------
     * OBSERVERS
     * ------------------------------------------------------------ */

    /**
     * MySQL DATETIME created_at and updated_at
     */
    public function created_at($row)
    {
        if (is_object($row))
        {
            $row->created_at = date('Y-m-d H:i:s');
        }
        else
        {
            $row['created_at'] = date('Y-m-d H:i:s');
        }

        return $row;
    }

    public function updated_at($row)
    {
        if (is_object($row))
        {
            $row->updated_at = date('Y-m-d H:i:s');
        }
        else
        {
            $row['updated_at'] = date('Y-m-d H:i:s');
        }

        return $row;
    }

    /**
     * Serialises data for you automatically, allowing you to pass
     * through objects and let it handle the serialisation in the background
     */
    public function serialize($row)
    {
        foreach ($this->callback_parameters as $column)
        {
            $row[$column] = serialize($row[$column]);
        }

        return $row;
    }

    public function unserialize($row)
    {
        foreach ($this->callback_parameters as $column)
        {
            if (is_array($row))
            {
                $row[$column] = unserialize($row[$column]);
            }
            else
            {
                $row->$column = unserialize($row->$column);
            }
        }

        return $row;
    }

    /**
     * Protect attributes by removing them from $row array
     */
    public function protect_attributes($row)
    {
        foreach ($this->protected_attributes as $attr)
        {
            if (is_object($row))
            {
                unset($row->$attr);
            }
            else
            {
                unset($row[$attr]);
            }
        }

        return $row;
    }

    /* --------------------------------------------------------------
     * QUERY BUILDER DIRECT ACCESS METHODS
     * ------------------------------------------------------------ */

    /**
     * A wrapper to $this->_database->order_by()
     */
    public function order_by($criteria, $order = 'ASC')
    {
        if ( is_array($criteria) )
        {
            foreach ($criteria as $key => $value)
            {
                $this->_database->order_by($key, $value);
            }
        }
        else
        {
            $this->_database->order_by($criteria, $order);
        }
        return $this;
    }

    /**
     * A wrapper to $this->_database->limit()
     */
    public function limit($limit, $offset = 0)
    {
        $this->_database->limit($limit, $offset);
        return $this;
    }

    /**
     * A wrapper to $this->_database->group_by()
     */
    public function group_by($fields)
    {
        $this->_database->group_by($fields);
        return $this;
    }

    /* --------------------------------------------------------------
     * INTERNAL METHODS
     * ------------------------------------------------------------ */

    /**
     * Trigger an event and call its observers. Pass through the event name
     * (which looks for an instance variable $this->event_name), an array of
     * parameters to pass through and an optional 'last in interation' boolean
     */
    public function trigger($event, $data = FALSE, $last = TRUE)
    {
        if (isset($this->$event) && is_array($this->$event))
        {
            foreach ($this->$event as $method)
            {
                if (strpos($method, '('))
                {
                    preg_match('/([a-zA-Z0-9\_\-]+)(\(([a-zA-Z0-9\_\-\., ]+)\))?/', $method, $matches);

                    $method = $matches[1];
                    $this->callback_parameters = explode(',', $matches[3]);
                }

                $data = call_user_func_array(array($this, $method), array($data, $last));
            }
        }

        return $data;
    }

    public function error_array(){
        return $this->form_validation->error_array();
    }


    /**
     * Run validation on the passed data
     */
    public function validate($data, $group = 'default')
    {
        if($this->skip_validation)
        {
            return $data;
        }

        $this->_build_validation_groups();

        if(isset($this->validate_groups[$group]) && !empty($this->validate))
        {


            foreach($data as $key => $val)
            {
                $_POST[$key] = $val;
            }

            $this->load->library('form_validation');

            if(is_array($this->validate_groups[$group]))
            {
                $this->form_validation->set_rules($this->validate_groups[$group]);
                if ($this->form_validation->run() === TRUE)
                {
                    return $data;
                }
                else
                {
                    return FALSE;
                }
            }
            else
            {
                /**
                 * @todo Verificar necessidade dessa linha
                 */
                if ($this->form_validation->run($this->validate_groups[$group]) === TRUE)
                {
                    return $data;
                }
                else
                {
                    return FALSE;
                }
            }
        }
        else
        {
            return $data;
        }
    }

    /**
     * Guess the table name by pluralising the model name
     */
    private function _fetch_table()
    {
        if ($this->_table == NULL)
        {
            $this->_table = plural(preg_replace('/(_m|_model)?$/', '', strtolower(get_class($this))));
        }
    }

    /**
     * Guess the primary key for current table
     */
    private function _fetch_primary_key()
    {
        if($this->primary_key == NULl)
        {
            $this->primary_key = $this->_database->query("SHOW KEYS FROM `".$this->_table."` WHERE Key_name = 'PRIMARY'")->row()->Column_name;
        }
    }
    /**
     * Seta parametro
     * @param $campo
     * @param $operacao
     * @param $parametro
     * @return $this
     */
    public function where($campo, $operacao, $parametro)
    {
        $this->_database->where("{$campo} {$operacao} '{$parametro}'");
        return $this;
    }


    /**
     * Set WHERE parameters, cleverly
     */
    protected function _set_where($params)
    {
        if (count($params) == 1 && is_array($params[0]))
        {
            foreach ($params[0] as $field => $filter)
            {
                if (is_array($filter))
                {
                    $this->_database->where_in($field, $filter);
                }
                else
                {
                    if (is_int($field))
                    {
                        $this->_database->where($filter);
                    }
                    else
                    {
                        $this->_database->where($field, $filter);
                    }
                }
            }
        }
        else if (count($params) == 1)
        {
            $this->_database->where($params[0]);
        }
        else if(count($params) == 2)
        {
            if (is_array($params[1]))
            {
                $this->_database->where_in($params[0], $params[1]);
            }
            else
            {
                $this->_database->where($params[0], $params[1]);
            }
        }
        else if(count($params) == 3)
        {
            $this->_database->where($params[0], $params[1], $params[2]);
        }
        else
        {
            if (is_array($params[1]))
            {
                $this->_database->where_in($params[0], $params[1]);
            }
            else
            {
                $this->_database->where($params[0], $params[1]);
            }
        }
    }

    /**
     * Return the method name for the current return type
     */
    protected function _return_type($multi = FALSE)
    {
        $method = ($multi) ? 'result' : 'row';
        return $this->_temporary_return_type == 'array' ? $method . '_array' : $method;
    }


    private function _build_validation_groups(){

        if(!empty($this->validate))
        {

            foreach($this->validate as $field)
            {
                if(isset($field['groups'])){

                    $groups = explode(',', $field['groups']);

                    unset($field['groups']);

                    foreach($groups as $group){

                        $group = trim($group);

                        $this->validate_groups[$group][] = $field;
                    }

                }else {

                    $this->validate_groups['default'][] = $field;
                }
            }

        }

    }

    public function primary_key (){

        return $this->primary_key;
    }

    protected function update_timestamps($row){

        $row[$this->update_at_key] = date('Y-m-d H:i:s');
        return $row;
    }

    protected function create_timestamps($row)
    {
        $row[$this->create_at_key] = $row[$this->update_at_key] = date('Y-m-d H:i:s');
        return $row;
    }

    protected function to_uppercase($row)
    {

        foreach ($this->fields_uppercase as $item) {
            if(isset($row[$item]) && !empty($row[$item])) {
                $row[$item] = mb_strtoupper($row[$item], 'UTF-8');
            }
        }
        return $row;
    }

    protected function to_lowercase($row)
    {

        foreach ($this->fields_lowercase as $item) {
            if(isset($row[$item]) && !empty($row[$item])) {
                $row[$item] = mb_strtolower($row[$item], 'UTF-8');
            }
        }
        return $row;
    }


    public function validate_form($group = 'default'){

        return $this->validate($_POST, $group);

    }

    /**
     * Retorna dados do formulário
     * @return array
     */
    public function get_form_data($just_check = false)
    {
        $data = array();

        //Para cada dado
        foreach($this->validate as $dado)
        {
            //Checa se o dado foi inserido
            if(!isset($dado['ignore']) && !isset($dado['type']))
            {
                //Seta dado
                $data[$dado['field']] = $this->input->post($dado['field']);
            }
            else if(isset($dado['type']))
            {

                //Se for um tipo arquivo
                if($dado['type'] == "file")
                {
                    if(!$just_check) //Caso for apenas para checar se é válido (Não realiza update)
                    {
                        if(!$this->input->post("sem_arquivo"))
                        {
                            //Resgata arquivo
                            $file = $this->do_upload($dado['field'], $this->upload_path);

                            //Se existir o arquivo
                            if(!is_null($file))
                            {
                                $data[$dado['field']] = $file; //Seta arquivo
                            }
                        }
                        else
                        {
                            $data[$dado['field']] = ""; //Seta arquivo
                        }

                    }
                }

                //Se for do tipo data
                else if ($dado['type'] == 'date')
                {
                    $data[$dado['field']] = app_date_mask_to_mysql($this->input->post($dado['field']).' 00:00:00');
                }

                //Se for campo ativo apenas resgata valor
                else if($dado['type'] == 'active')
                {
                    $data[$dado['field']] = $this->input->post($dado['field']);
                }

                //Se for array
                else if ($dado['type'] == 'array')
                {
                    $dados = $this->input->post($dado['field']);

                    if(is_array($dados))
                    {
                        $data[$dado['field']] = "";
                        foreach ($dados as $value)
                        {
                            $data[$dado['field']] .= "{$value},";
                        }
                    }

                }
                else
                {
                    exit("Tipo de dado não definido");
                }
            }
        }
        return $data;
    }

    /**
     * Realiza Upload
     * @param $name
     * @param $path
     * @return null
     */
    public function do_upload($name, $path = null)
    {
        if($path == null)
            $path = "";

        $pasta = UPLOAD_PATH . "/{$path}";

        //Caso diretório não exista ele cria
        if(!file_exists($pasta))
        {
            mkdir($pasta, 0777, true);
        }

        //Carrega configurações
        $config['upload_path'] = $pasta;
        $config['allowed_types'] = "*";
        $config['max_size'] = 0;
        $config['encrypt_name'] = true;

        //Carrega biblioteca de upload
        $this->load->library('upload', $config);

        if(isset($this->thumb_size))
        {
            $this->upload->thumbs = $this->thumb_size;
        }

        //Reseta erros
        $this->upload->error_msg = array();

        $multi = false;

        if(isset($_FILES[$name]['name']))
        {
            if($_FILES && is_array($_FILES[$name]['name']))
            {
                //Realiza upload
                $this->upload->do_multi_upload($name);

                //Realiza upload da imagem
                $file = $this->upload->get_multi_upload_data();
                $multi = true;
            }
            else
            {
                //Realiza upload
                $this->upload->do_upload($name);

                //Realiza upload da imagem
                $file = $this->upload->data();
            }


            //Caso retorne erros
            if ($this->upload->display_errors())
            {
                return null; //Seta nulo
            }
            else
            {
                if($multi)
                {
                    if(sizeof($_FILES[$name]['name']) == 1)
                    {
                        $arquivo = str_replace('.tif','.jpg', $file[0]['file_name']);
                    }
                    else
                    {
                        foreach($file as $f)
                        {
                            $arquivo[] = str_replace('.tif','.jpg', $f['file_name']);
                        }
                    }

                }
                else
                {
                    $arquivo = str_replace('.tif','.jpg', $file['file_name']);
                }

                return $arquivo; //Retorna nome da imagem
            }
        }
        return null;


    }

    public function insert_form($array = array()){

        $data = $this->get_form_data();
        $data = array_merge($data, $array);
        return $this->insert($data, TRUE);

    }

    /**
     * Realiza UPDATE do formulário
     * @return bool
     */
    public function update_form()
    {
        $data = $this->get_form_data();
        return $this->update($this->input->post($this->primary_key),  $data, TRUE);
    }
    public function with_simple_relation_foreign($with_table, $prefix , $foreing_key1, $foreign_key2 , $fields, $join = 'inner'){


        if(is_array($with_table)){

        }


        foreach($fields as $field){

            $this->_database->select("{$with_table}.$field AS {$prefix}{$field}");
        }

        $this->_database->join($with_table, $this->_table.".{$foreing_key1} = {$with_table}.{$foreign_key2}", $join);

        return $this;

    }

    public function with_simple_relation($with_table, $prefix , $foreing_key , $fields, $join = 'inner'){


        if(is_array($with_table)){

        }


        foreach($fields as $field){

            $this->_database->select("{$with_table}.$field AS {$prefix}{$field}");
        }

        $this->_database->join($with_table, $this->_table.".{$foreing_key} = {$with_table}.{$foreing_key}", $join);

        return $this;

    }

    function insert_array($data){

        foreach($data as $row){
            $this->insert($row);
        }
    }

    public function  set_select(){

        $this->_database->select($this->_table . '.*');

        return $this;
    }

    function check_row_exists($field, $value){


        $this->_database->where($this->_table . ".{$field}", $value);

        $rows = $this->get_all();

        if ($rows) {

            return true;

        } else {
            return false;
        }

    }

    public function get_one(){

        $this->limit(1);
        $rows = $this->get_all();

        if($rows){

            return $rows[0];
        }

        return false;
    }

    /**
     * @return array
     */
    public function getAllFields()
    {
        //Get data
        $data = $this->validate;
       // print_r($data);exit;
        $result = array();

        foreach ($data as $value) {
            //Checa se possui propriedade foreign_table
            if (isset($value['foreign']) || isset($value['inverse_foreign']))
            {
                if (isset($value['foreign']))
                    $table = $value['foreign'];
                else
                    $table = $value['inverse_foreign'];

                //Carrega model
                $this->load->model("{$table}_model", "{$table}_model");

                //Carrega campo
                $campo_tabela = $this->{$table . '_model'}->get_form_data();
                $keys = array_keys($campo_tabela);
                $campos = array();

                $i = 0;
                foreach ($keys as $campo)
                {
                    $campos["{$table}.{$campo}"] = false;
                }

                $result = array_merge($result, $campos);

            }
        }

        $result = array_merge($result, $this->get_form_data());


        return $result;
    }

    /**
     * Com estrangeiras
     * @param array $campos
     * @return $this
     */
    public function with_foreign($campos = array())
    {
        //Get data
        $data = $this->validate;

        $campo_tabela = array();
        //Para cada campo
        foreach($data as $value)
        {
            //Checa se possui propriedade foreign_table
            if(isset($value['foreign']))
            {
                //Carrega model
                $this->load->model("{$value['foreign']}_model", "{$value['foreign']}_model");

                //Carrega campo
                $campo_tabela = array_keys($this->{$value['foreign'] . '_model'}->get_form_data());

                //Realiza relação simples
                $join = (isset($value['foreign_join'])) ? $value['foreign_join'] : null;
                $foreign_key = (isset($value['foreign_key'])) ? $value['foreign_key'] : "{$value['foreign']}_id";
                //$this->with_simple_relation($value['foreign'], "{$value['foreign']}_", $foreign_key, $campo_tabela, $join);

                $this->with_simple_relation_foreign($value['foreign'], "{$value['foreign']}_", "{$value['field']}", $foreign_key, $campo_tabela, $join);

            }
            else if(isset($value['inverse_foreign']))
            {
                //Carrega model
                $this->load->model("{$value['inverse_foreign']}_model", "{$value['inverse_foreign']}_model");

                //Carrega campo
                $campo_tabela = array_keys($this->{$value['inverse_foreign'] . '_model'}->get_form_data());

                //Realiza relação simples
                $this->with_simple_relation($value['inverse_foreign'], "{$value['inverse_foreign']}_", "{$this->primary_key()}", $campo_tabela, "left");
            }
            $this->obj_count_foreign++;
        }
        return $this;
    }
    /*
    public function with_foreign($campos = array())
    {
        //Get data
        $data = $this->validate;

        $campo_tabela = array();
        //Para cada campo
        foreach($data as $value)
        {
            //Checa se possui propriedade foreign_table
            if(isset($value['foreign']))
            {
                //Carrega model
                $this->load->model("{$value['foreign']}_model", "{$value['foreign']}_model");

                //Carrega campo
                $campo_tabela = array_keys($this->{$value['foreign'] . '_model'}->get_form_data());

                //Realiza relação simples
                $this->with_simple_relation($value['foreign'], "{$value['foreign']}_", "{$value['foreign']}_id", $campo_tabela);

            }
            else if(isset($value['inverse_foreign']))
            {
                //Carrega model
                $this->load->model("{$value['inverse_foreign']}_model", "{$value['inverse_foreign']}_model");

                //Carrega campo
                $campo_tabela = array_keys($this->{$value['inverse_foreign'] . '_model'}->get_form_data());

                //Realiza relação simples
                $this->with_simple_relation($value['inverse_foreign'], "{$value['inverse_foreign']}_", "{$this->primary_key()}", $campo_tabela, "left");
            }
            $this->obj_count_foreign++;
        }
        return $this;
    }
    */

    public function distinct(){
        $this->_database->distinct();
        return $this;


    }


    public function last_query(){
        return $this->_database->last_query();
    }






    public function filterFromInput($filter = null, $data = null, $thisTable = true, $or = true)
    {
        $this->_database->distinct();

        if(is_null($filter))
            $filter = $this->input->get();


        if(is_null($data))
            $data = array_keys($this->getAllFields());


        foreach ($data as $field)
        {

            if(strpos($field, '.') === FALSE){

                $tabela = $this->_table;

            }else{
                $juntos = explode('.', $field);
                $tabela = $juntos[0];
                $field = $juntos[1];
            }



            if (isset($filter[$field]) && ($filter[$field] != ""))
            {
                if(is_array($filter[$field]))
                {
                    foreach ($filter[$field] as $busca)
                    {
                        if($thisTable)
                        {
                            if($or)
                                $this->_database->or_like($tabela.".{$field}", $busca);
                            else
                                $this->_database->like($tabela.".{$field}", $busca);
                        }
                        else
                        {
                            if($or)
                                $this->_database->or_like($field, $busca);
                            else
                                $this->_database->like($field, $busca);

                        }
                    }
                }
                else
                {

                    if($thisTable)
                    {
                        if($or)
                            $this->_database->or_like($tabela.".{$field}", $filter[$field]);
                        else
                            $this->_database->like($tabela.".{$field}", $filter[$field]);
                    }
                    else
                    {
                        if($or)
                            $this->_database->or_like($field, $busca);
                        else
                            $this->_database->like($field, $busca);
                    }
                }

            }
        }
        return $this;
    }


    public function processa_parceiros_permitidos( $campo ) {
        $parceiro_id = $this->session->userdata('parceiro_id');
        $parceiros_permitidos = $this->session->userdata("parceiros_permitidos");
        $parceiro_selecionado = $this->session->userdata("parceiro_selecionado");


        if( $parceiro_id ) {
            if( $parceiro_selecionado ) {
                $this->where( $campo, '=', $parceiro_selecionado );
            } else if( $parceiros_permitidos ) {
                $sql = "";
                //Busca pelos filhos
                foreach( $parceiros_permitidos as $parceiro_id ) {
                    $sql .= " OR {$campo} = {$parceiro_id}";
                }
                $sql = substr($sql, 3);
                $this->_database->where("($sql)");
            } else {
                $this->where($campo, '=', $parceiro_id);
            }
        }

    }

    public function disableLog(){
        $this->enable_log = FALSE;
    }

}

