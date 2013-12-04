<?php
/**
 * General query builder configuration layer
 * Loads the preset query builder
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014  Rachelle Scheijen
 * @author    Rachelle Scheijen
 * @since     1.0
 * @changed   07/09/13
 *
 * Scripthulp framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Scripthulp framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Scripthulp framework.  If not, see <http://www.gnu.org/licenses/>.
 */

class Service_QueryBuilder extends Service{
	private $obj_builder;
	private $s_type;

	/**
	 * Includes the correct DAL
	 */
	public function __construct(){

		$service_XmlSettings    = Memory::services('XmlSettings');

		/* databasetype */
		$this->s_type           = ucfirst($service_XmlSettings->get('settings/SQL/type'));
		$this->loadBuilder();
	}

	/**
	 * Destructor
	 */
	public function __destruct(){
		$this->service_Memory  = null;
		$this->s_type		= null;
	}

	/**
	 * Loads the selected Builder
	 */
	private function loadBuilder(){
		require_once(NIV.'include/database/builder_'.$this->s_type.'.inc.php');

		$service_Database	= Memory::services('Database');
		$service_Database->defaultConnect();
	}
	
	public function createBuilder(){
		if( is_null($this->obj_builder) ){
			$s_name     = 'Builder_'.$this->s_type;
			$this->obj_builder    = new $s_name();
		}
		
		return $this->obj_builder;
	}
}

interface Builder {
	/**
	 * Shows the tables in the current database
	 */
	public function showTables();
	
	/**
	 * Shows the databases that the user has access to
	 */
	public function showDatabases();
	
	/**
	 * Creates a select statement
	 *
	 * @param		String	$s_table	The table name
	 * @param		String	$s_fields	The field names sepperated with a ,
	 */
	public function select($s_table,$s_fields);
	
	/**
	 * Creates a insert statement
	 *
	 * @param		String	$s_table	The table name
	 * @param		array	$a_fields		The field names, also accepts a single value
	 * @param		array	$a_types	The value types : l (SQL, no parse), i (int) ,d (double) ,s (string) or b (blob), also accepts a single value
	 * @param		array	$a_values		The values, also accepts a single value
	 */
	public function insert($s_table,$a_fields,$a_types,$a_values);
	
	/**
	 * Creates a update statement
	 *
	 * @param		String	$s_table	The table name
	 * @param		array		$a_fields	The field names, also accepts a single value
	 * @param		array		$a_types	The value types : l (SQL, no parse), i (int) ,d (double) ,s (string) or b (blob), also accepts a single value
	 * @param		array		$a_values	The values, also accepts a single value
	 */
	public function update($s_table,$a_fields,$a_types,$a_values);
	
	/**
	 * Creates a delete statement
	 *
	 * @param	String	$s_table	The table name
	 */
	public function delete($s_table);
	
	/**
	 * Returns the create table generation class
	 * 
	 * @param String $s_table			The table name
	 * @param Boolean $bo_dropTable	Set to true to drop the given table before creating it
	 * @return Create The create table generation class
	 */
	public function getCreate($s_table,$bo_dropTable);
	
	/**
	 * Adds a inner join between 2 tables
	 * 
	 * @param		String	$s_table	The table name
	 * @param		String	$s_field1	The field from the first table
	 * @param		String	$s_field2	The field from the second table
	 */
	public function innerJoin($s_table,$s_field1,$s_field2);
	
	/**
	 * Adds a outer join between 2 tables
	 *
	 * @param		String	$s_table	The table name
	 * @param		String	$s_field1	The field from the first table
	 * @param		String	$s_field2	The field from the second table
	 */
	public function outerJoin($s_table,$s_field1,$s_field2);
	
	/**
	 * Adds a left join between 2 tables
	 *
	 * @param		String	$s_table	The table name
	 * @param		String	$s_field1	The field from the first table
	 * @param		String	$s_field2	The field from the second table
	 */
	public function leftJoin($s_table,$s_field1,$s_field2);
	
	/**
	 * Adds a right join between 2 tables
	 *
	 * @param		String	$s_table	The table name
	 * @param		String	$s_field1	The field from the first table
	 * @param		String	$s_field2	The field from the second table
	 */
	public function rightJoin($s_table,$s_field1,$s_field2);
	
	/**
	 * Returns the where generation class
	 * 
	 * @return Where	The where generation class
	 */
	public function getWhere();
	
	/**
	 * Adds a limitation to the query statement
	 * Only works on select, update and delete statements
	 *
	 * @param		int	$i_limit	The limitation of records
	 * @param		int	$i_offset	The offset to start from, default 0 (first record)
	 */
	public function limit($i_limit,$i_offset = 0);

	/**
	 * Groups the results by the given field
	 * 
	 * @param		String	$s_field	The field
	 */
	public function group($s_field);
	
	/**
	 * Returns the having generation class
	 * 
	 * @return Having		The having generation class
	 */
	public function getHaving();
	
	/**
	 * Orders the records in the given order
	 * 
	 * @param		String	$s_field1		The first field to order on
	 * @param		String	$s_ordering1	The ordering method (ASC|DESC)
	 * @param		String	$s_field2		The second field to order on, optional
	 * @param		String	$s_ordering2	The ordering method (ASC|DESC), optional
	 */
	public function order($s_field1,$s_ordering1='ASC',$s_field2='',$s_ordering2='ASC');
	
	/**
	 * Return the total amount statement for the given field
	 * 
	 * @param 	String $s_field	The field name
	 * @param 	String $s_alias	The alias, default the field name
	 * @return String		The statement
	 */
	public function getSum($s_field,$s_alias='');
	
	/**
	 * Return the maximun value statement for the given field
	 * 
	 * @param 	String $s_field	The field name
	 * @param 	String $s_alias	The alias, default the field name
	 * @return String		The statement
	 */
	public function getMaximun($s_field,$s_alias='');
	
	/**
	 * Return the minimun value statement for the given field
	 *
	 * @param String $s_field		The field name
	 * @param String $s_alias		The alias, default the field name
	 * @return String		The statement
	 */
	public function getMinimun($s_field,$s_alias='');
	
	/**
	 * Return the average value statement for the given field
	 *
	 * @param String $s_field		The field name
	 * @param String $s_alias		The alias, default the field name
	 * @return String		The statement
	 */
	public function getAverage($s_field,$s_alias='');
	
	/**
	 * Return statement for counting the number of records on the given field
	 *
	 * @param String $s_field		The field name
	 * @param String $s_alias		The alias, default the field name
	 * @return String		The statement
	 */
	public function getCount($s_field,$s_alias='');
	
	/**
	 * Returns the query result
	 * 
	 * @return DAL		The query result as a database object
	 */
	public function getResult();
	
	/**
	 * Builds the query
	 */
	public function render();
	
	/**
	 * Starts a new transaction
	 *
	 * @throws DBException	If a transaction is allready active
	*/
	public function transaction();
	
	/**
	 * Commits the current transaction
	 *
	 * @throws DBException	If no transaction is active
	*/
	public function commit();
	
	/**
	 * Rolls the current transaction back
	 *
	 * @throws DBException	If no transaction is active
	*/
	public function rollback();
}

interface Where {
	/**
	 * Resets the class Where
	 */
	public function reset();
	
	/**
	 * Adds fields with an and relation
	 * 
	 * @param		array $a_fields		The fields,also accepts a single value 
	 * @param		array	$a_types	The value types : l (SQL, no parse), i (int) ,d (double) ,s (string) or b (blob), also accepts a single value	
	 * @param		array	$a_values		The values, also accepts a single value
	 * @param array		$a_keys			The keys (=|<>|<|>|LIKE|IN|BETWEEN), also accepts a single value. leave empty for =
	 * @throws DBException		If the key is invalid
	 */
	public function addAnd($a_fields,$a_types,$a_values,$a_keys);
	
	/**
	 * Adds fields with an or relation
	 * 
	 * @param		array $a_fields		The fields,also accepts a single value 
	 * @param		array	$a_types	The value types : l (SQL, no parse), i (int) ,d (double) ,s (string) or b (blob), also accepts a single value
	 * @param		array	$a_values		The values, also accepts a single value
	 * @param array		$a_keys			The keys (=|<>|<|>|LIKE|IN|BETWEEN), also accepts a single value. leave empty for =
	 * @throws DBException		If the key is invalid
	 */
	public function addOr($a_fields,$a_types,$a_values,$a_keys);
	
	/**
	 * Starts a sub where part
	 */
	public function startSubWhere();
	
	/**
	 * Ends a sub where part
	 */
	public function endSubWhere();
	
	/**
	 * Adds a sub query
	 * 
	 * @param		Builder	$obj_builder	The builder object
	 * @param 	String	$s_field			The field
	 * @param 	String	$s_key				The key (=|<>|LIKE|IN|BETWEEN)
	 * @param		String 	$s_command		The command (AND|OR)	
	 * @throws DBException		If the key is invalid
	 * @throws DBException		If the command is invalid
	 */
	public function addSubQuery($obj_builder,$s_field,$s_key,$s_command);
	
	/**
	 * Renders the where
	 * 
	 * @return array		The where
	 */
	public function render();
}

interface Having {
	/**
	 * Resets the class Having
	 */
	public function reset();
	
	/**
	 * Adds fields with an and relation
	 * 
	 * @param array 	$a_fields		The fields,also accepts a single value 
	 * @param		array	$a_types	The value types : l (SQL, no parse), i (int) ,d (double) ,s (string) or b (blob), also accepts a single value		
	 * @param	 array	$a_values		The values, also accepts a single value
	 * @param array		$a_keys			The keys (=|<>|<|>|LIKE|IN|BETWEEN), also accepts a single value. leave empty for =
	 * @throws DBException		If the key is invalid
	 */
	public function addAnd($a_fields,$a_types,$a_values,$a_keys);
	
	/**
	 * Adds fields with an or relation
	 * 
	 * @param array 	$a_fields		The fields,also accepts a single value 
	 * @param		array	$a_types	The value types : l (SQL, no parse), i (int) ,d (double) ,s (string) or b (blob), also accepts a single value	
	 * @param	 array	$a_values		The values, also accepts a single value
	 * @param array		$a_keys			The keys (=|<>|<|>|LIKE|IN|BETWEEN), also accepts a single value. leave empty for =
	 * @throws DBException		If the key is invalid
	 */
	public function addOr($a_fields,$a_types,$a_values,$a_keys);
	
	/**
	 * Starts a sub having part
	 */
	public function startSubHaving();
	
	/**
	 * Ends a sub having part
	 */
	public function endSubHaving();
	
	/**
	 * Renders the having
	 * 
	 * @return array		The having
	 */
	public function render();
}

interface Create {
	public function reset();
	
	/**
	 * Creates a table
	 * 
	 * @param	String	$s_table	The table name
	 * @param	Boolean $bo_dropTable	Set to true to drop the given table before creating it
	 */
	public function setTable($s_table,$bo_dropTable = false);
	
	/**
	 * Adds a field to the create stament
	 * 
	 * @param String $s_field			The field name
	 * @param String $s_type			The field type (database type!)
	 * @param int	 $i_length			The length of the field, only for length fields
	 * @param String $s_default		The default value
	 * @param String $bo_signed		Set to true for signed value, default unsigned
	 * @param String $bo_null			Set to true for NULL allowed
	 * @param String $bo_autoIncrement	Set to true for auto increment
	 */
	public function addRow($s_field,$s_type,$i_length,$s_default='',$bo_signed = false,$bo_null = false,$bo_autoIncrement = false);
	
	/**
	 * Adds an enum field to the create stament
	 *
	 * @param String $s_field			The field name
	 * @param array	 	$a_values		The values
	 * @param String $s_default		The default value
	 * @param String $bo_null			Set to true for NULL allowed
	 */
	public function addEnum($s_field,$a_values,$s_default,$bo_null = false);
	
	/**
	 * Adds a set field to the create stament
	 *
	 * @param String $s_field			The field name
	 * @param array	 	$a_values		The values
	 * @param String $s_default		The default value
	 * @param String $bo_null			Set to true for NULL allowed
	 */
	public function addSet($s_field,$s_values,$s_default,$bo_null = false);
	
	/**
	 * Adds a primary key to the given field
	 * 
	 * @param String $s_field		The field name
	 * @throws DBException If the field is unknown or if the primary key is allready set
	 */
	public function addPrimary($s_field);
	
	/**
	 * Adds a index to the given field
	 *
	 * @param String $s_field		The field name
	 * @throws DBException If the field is unknown
	 */
	public function addIndex($s_field);
	
	/**
	 * Sets the given fields as unique
	 *
	 * @param String $s_field		The field name
	 * @throws DBException If the field is unknown
	 */
	public function addUnique($s_field);
	
	/**
	 * Sets full text search on the given field
	 *
	 * @param String $s_field		The field name
	 * @throws DBException If the field is unknown
	 * @throws DBException If the field type is not VARCHAR and not TEXT.
	 */
	public function addFullTextSearch($s_field);

	/**
	 * Returns the drop table setting
	 *
	 * @return String	The drop table command. Empty string for not dropping
	 */
	public function getDropTable();
	
	/**
	 * Creates the query
	 * 
	 * @return String		The query
	 */
	public function render();
}
?>
