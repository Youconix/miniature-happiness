<?php

namespace core\database;

/**
 * MySQLi Database Access Layer 
 *
 * @author		REJ Scheijen rej-scheijen@scripthulp.com
 * @copyright	Messageboard bv  2008-2012
 * @since		01/10/2008
 * @changed     04/11/11
 */
class Database_Mysqli_binded {  
	private $obj_caller;
	public $i_affected_rows;
	public $i_id;
	public $obj_query = null;
	private $a_result = array();
	
	private $s_types;
	private $a_values;
	
	public function __construct($obj_caller){
		$this->obj_caller	= $obj_caller;
	}
	
    /**
     * Clears the previous result set 
     */
    public function clearResult() {
    	if( !is_null($this->obj_query) ){
        	$this->obj_query->free_result();
            $this->obj_query->close();
            
            $this->obj_query	= null;
        }
    }
    
    /**
     * Resets the data result pointer 
     */
    protected function resetPointer(){
        if( !is_null($this->obj_query) )
            $this->obj_query->data_seek(0);
    }
    
    /**
     * Process the binded query
     *
     * @param	 string	$s_query	The query to excequte
     * @param	 array	$a_types	The value types : i (int) ,d (double) ,s (string) or b (blob), also accepts a single value
     * @param	 array  $a_values	The values, also accepts a single value
     * @param	 object	$link		The database link
     * @throws  Exception if the arguments are illegal
     * @return	object	The query object
     */
    public function queryBindedProcess($s_query,$a_types,$a_values,$link){    
		$this->obj_query = null;
    	
    	$query = $link->stmt_init();
    	
    	if ( !$query->prepare($s_query) ) {
    		throw new \DBException("Query failed : " . $link->error. '.\n' . $s_query);
    	}
    	
    	if( !is_array($a_types) )       $a_types = array($a_types);
        if( !is_array($a_values) )      $a_values = array($a_values);
    	
    	$a_params	= array(0=>'');
    	$i_num	= count($a_types);
    	for($i=0; $i<$i_num; $i++){
    		$s_type	= $a_types[$i];
    
    		if( !is_string($s_type) || !in_array($s_type,array('i','d','s','b')) )
    			throw new \Exception('Illegal binding type '.$s_type.'  Only i (int), d (double), s (string) and b (blob) is allowed.');
    
    		$a_params[0]	.= $s_type;
    		$a_params[]	= $a_values[$i];
    	}
    	
    	$callable = array($query, 'bind_param');
    	call_user_func_array($callable, $this->refValues($a_params));
    	 
    	$res	= $query->execute();

    	if ($res === false) {
    		throw new \DBException("Query failed : " . $link->error. '.\n' . $s_query);
    	}
    	
    	$s_command	= strtoupper( trim( substr($s_query, 0,strpos($s_query,' ')) ));
    	if( $s_command == 'SELECT' || $s_command == 'SHOW' || $s_command == 'ANALYZE' ){
    		$this->a_result	= null; // force cleaning
    		
    		$query->store_result();
    		
    		$obj_meta = $query->result_metadata();
    		$a_params	= array();
    		while ($field = $obj_meta->fetch_field())
    		{
    			$a_params[] = &$this->a_result[$field->name];
    		}
    		
    		call_user_func_array(array($query, 'bind_result'), $a_params);
    		
    		$this->obj_query = $query;
    	}
    	else if( $s_command == 'INSERT' ) {
    		$this->i_id = $query->insert_id;
    	} 
    	else if( $s_command == 'UPDATE' || $s_command == 'DELETE' ){
    		$this->i_affected_rows = $query->affected_rows;
    	}
    }
    
    /**
     * Callback to bind the parameters to the query
     * 
     * @param	array $a_arguments		The arguments
     * @return array	The arguments
     */
    private function refValues($a_arguments){
    	if (strnatcmp(phpversion(),'5.3') >= 0){ //Reference is required for PHP 5.3+    	
    		$a_refs = array();
    		foreach($a_arguments as $s_key => $value)
    			$a_refs[$s_key] = &$a_arguments[$s_key];
    		return $a_refs;
    	}
    	return $a_arguments;
    }

    /**
     * Returns the result from the query with the given row and field
     *
     * @param   int     The row
     * @param   string  The field
     * @return  string  The content of the requested result-field
     * @throws  DBException when no SELECT-query was excequeted
     * @throws  DBException if the row is not present (see num_rows())
     * @throws  DBException when the field is not present
     */
    public function result($i_row, $s_field) {
        $this->resetPointer();
        
        $i_rows = $this->obj_caller->num_rows();
        if( $i_row >= $i_rows ){
            throw new \DBException("Unable to fetch row ".$i_row." Only ".$i_rows." are present");
        }
        
        $this->obj_query->data_seek($i_row);
        $a_data = $this->fetch_assoc();
        
        if( !array_key_exists($s_field, $a_data[0]) ){
            throw new \DBException("Unable to fetch the unknown field ".$s_field);
        }

        return $a_data[0][$s_field];
    }

    /**
     * Returns the results of the query in a associate and numeric array
     *
     * @return  array   The data-set with numeric keys and named keys
     * @throws  DBException when no SELECT-query was excequeted
     */
    public function fetch_array() {
        $this->resetPointer();

        $a_result	= array();
        while ( $this->obj_query->fetch()) {
        	$i_field = 0;
        	$a_temp = array();
        	foreach($this->a_result AS $s_key => $value){
        		$a_temp[$i_field]	= $value;
        		$a_temp[$s_key]	= $value;
        		$i_field++;
        	}
            $a_result[]	= $a_temp;
        }

        return $a_result;
    }

    /**
     * Returns the results of the query in a associate array
     *
     * @return  array   The data-set with named keys
     * @throws  DBException when no SELECT-query was excequeted
     */
    public function fetch_assoc() {
        $this->resetPointer();

        $a_result	= array();
        
        while ( $this->obj_query->fetch()) {
        	$a_temp = array();
        	foreach($this->a_result AS $s_key => $value){
        		$a_temp[$s_key]	= $value;
        	}
            $a_result[]	= $a_temp;
        }

        return $a_result;
    }

    /**
     * Returns the results of the query in a associate array with the given field as counter-key
     *
     * @param   string  The field that is the counter-key
     * @return  array	The data-set with named keys sorted on the given key
     * @throws  DBException when no SELECT-query was excequeted
     */
    public function fetch_assoc_key($s_key) {
        $this->resetPointer();

        $a_result	= array();
        while ( $this->obj_query->fetch()) {
        	$a_temp = array();
        	foreach($this->a_result AS $s_fieldkey => $value){
        		$a_temp[$s_fieldkey]	= $value;
        		
        		if( $s_fieldkey == $s_key )
        			$s_rowKey	= $value;
        	}
            $a_result[$s_rowKey]	= $a_temp;
        }

        return $a_result;
    }

    /**
     * Returns the results of the query as a object-array
     *
     * @return  object  The data-set as a object
     * @throws  DBException when no SELECT-query was excequeted
     */
    public function fetch_object() {
        $this->resetPointer();

        $a_temp = array();
        while ($obj_res = $this->obj_query->fetch_object() ) {
            $a_temp[] = $a_obj;
        }

        return $a_temp;
    }
    
    /**
     * Returns the results of the query as a numeric-array
     *
     * @return  object  The data-set as a array
     * @throws  DBException when no SELECT-query was excequeted
     */
    public function fetch_row(){
        $this->resetPointer();
        
    	$a_result	= array();
        while ( $this->obj_query->fetch()) {
        	$i_field = 0;
        	$a_temp = array();
        	foreach($this->a_result AS $s_key => $value){
        		$a_temp[$i_field]	= $value;
        		$i_field++;
        	}
            $a_result[]	= $a_temp;
        }
        
        return $a_result;
    }
}

?>
