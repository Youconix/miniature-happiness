<?php
if (! interface_exists('DAL')) {
    if (! class_exists('\core\services\Service')) {
        require (NIV . 'core/services/Service.inc.php');
    }
    
    if (! class_exists('\core\database\Database')) {
        require (NIV . 'core/database/Database.inc.php');
    }
}

class DummyDAL implements \core\database\DAL
{

    public $i_numRows = 0;

    public $i_affectedRows = 0;

    public $a_data = array();

    public $i_insertID = 1;
    
    private $dataset;

    public function __construct() {
        $this->clearQueue();
    }
    
    public function __destruct()
    {}
    
    public function enqueueData($a_data) {
        $this->dataset->enqueue($a_data);
    } 
    
    public function dequeueData() {
        if ( $this->dataset->isEmpty() ) {
            $this->a_data = array();
        }
        else {
            $this->a_data = $this->dataset->dequeue();
        }
        $this->i_numRows = count($this->a_data);
    }
    
    public function clearQueue() {
        $this->dataset = new \SplQueue();
    }

    public function affected_rows()
    {
        return $this->i_affectedRows;
    }

    public function analyse($s_table)
    {}

    public static function checkLogin($s_username, $s_password, $s_database, $s_host = '127.0.0.1', $i_port = -1)
    {}

    public function commit()
    {}

    public function connection($s_username, $s_password, $s_database, $s_host = '127.0.0.1', $i_port = -1)
    {}

    public function connectionEnd()
    {}

    public function databaseExists($s_database)
    {
        return true;
    }

    public function defaultConnect()
    {}

    public function escape_string($s_data)
    {
        return $s_data;
    }

    public function fetch_array()
    {
        return $this->a_data;
    }

    public function fetch_assoc()
    {
        return $this->a_data;
    }

    public function fetch_assoc_key($s_key)
    {
        return $this->a_data;
    }

    public function fetch_object()
    {
        return $this->a_data;
    }

    public function fetch_row()
    {
        return $this->a_data;
    }

    public function getId()
    {
        return $this->i_insertID;
    }

    public function isConnected()
    {
        return true;
    }

    public function num_rows()
    {
        return $this->i_numRows;
    }

    public function optimize($s_table)
    {}

    public function query($s_query)
    {}

    public function queryBinded($s_query, $a_types, $a_values)
    {}

    public function repair($s_table)
    {}

    public function result($i_row, $s_field)
    {
        if( !array_key_exists($i_row, $this->a_data) ){
            throw new \DBException("Trying to get data from a not existing field");
        }
        if( !array_key_exists($s_field, $this->a_data[$i_row]) ){
            throw new \DBException("Trying to get data from a not existing field");
        }
        
        return $this->a_data[$i_row][$s_field];
    }

    public function rollback()
    {}

    public function transaction()
    {}

    public function useDB($s_database)
    {}
    
    public function describe($s_table, $bo_addNotExists = false, $bo_dropTable = false){}
}
?>