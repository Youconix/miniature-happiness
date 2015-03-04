<?php
namespace core\services;

/**
 * General query builder configuration layer
 * Loads the preset query builder
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 *       
 *        Miniature-happiness is free software: you can redistribute it and/or modify
 *        it under the terms of the GNU Lesser General Public License as published by
 *        the Free Software Foundation, either version 3 of the License, or
 *        (at your option) any later version.
 *       
 *        Miniature-happiness is distributed in the hope that it will be useful,
 *        but WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *        GNU General Public License for more details.
 *       
 *        You should have received a copy of the GNU Lesser General Public License
 *        along with Miniature-happiness. If not, see <http://www.gnu.org/licenses/>.
 */
class QueryBuilder extends Service
{

    private $service_Database;

    private $obj_builder;

    private $s_type;

    /**
     * Includes the correct DAL
     *
     * @param core\services\Settings $service_Settings
     *            The settings service
     * @param core\database\DAL $service_Database
     *            The DAL
     */
    public function __construct(\core\services\Settings $service_Settings, \core\database\DAL $service_Database)
    {
        $this->service_Database = $service_Database;
        
        /* databasetype */
        $this->s_type = ucfirst($service_Settings->get('settings/SQL/type'));
        $this->loadBuilder();
    }

    /**
     * Loads the selected Builder
     */
    private function loadBuilder()
    {
        require_once (NIV . 'core/database/builder_' . $this->s_type . '.inc.php');
        
        $this->service_Database->defaultConnect();
    }

    /**
     * Creates the builder
     *
     * @return Builder The builder
     */
    public function createBuilder()
    {
        if (is_null($this->obj_builder)) {
            $s_name = '\core\database\Builder_' . $this->s_type;
            $this->obj_builder = new $s_name($this->service_Database);
        }
        
        return $this->obj_builder;
    }
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
	* Adds a field that can be NULL
	*
	* @param		string	$s_field	The field's name
	* @param		array	$a_types	The possible value types
	* @param		string	$s_value	The value
	* @param		string	$s_default	The value if database returns NULL
	* @param		string	$s_key		The key (=|<>|<|>|LIKE|IN|BETWEEN). Leave empty for =
	* @param		string	$s_join		The join command for the where or having part
	* @throws		DBException		If the key is invalid.
	*/	
	public function addIsNull($s_field,$a_types,$s_value,$s_default,$s_key = '',$s_join = 'AND');
	
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
	 * @param		Builder	$obj_builder	The builder object=
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
	* Adds a field that can be NULL
	*
	* @param		string	$s_field	The field's name
	* @param		array	$a_types	The possible value types
	* @param		string	$s_value	The value
	* @param		string	$s_default	The value if database returns NULL
	* @param		string	$s_key		The key (=|<>|<|>|LIKE|IN|BETWEEN). Leave empty for =
	* @param		string	$s_join		The join command for the where or having part
	* @throws		DBException		If the key is invalid.
	*/	
	public function addIsNull($s_field,$a_types,$s_value,$s_default,$s_key = '',$s_join = 'AND');
	
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

interface Having
{

    /**
     * Resets the class Having
     */
    public function reset();

    /**
     * Adds fields with an and relation
     *
     * @param array $a_fields
     *            fields,also accepts a single value
     * @param array $a_types
     *            types : l (SQL, no parse), i (int) ,d (double) ,s (string) or b (blob), also accepts a single value
     * @param array $a_values
     *            also accepts a single value
     * @param array $a_keys
     *            (=|<>|<|>|LIKE|IN|BETWEEN), also accepts a single value. leave empty for =
     * @throws DBException the key is invalid
     */
    public function addAnd($a_fields, $a_types, $a_values, $a_keys);

    /**
     * Adds fields with an or relation
     *
     * @param array $a_fields
     *            fields,also accepts a single value
     * @param array $a_types
     *            types : l (SQL, no parse), i (int) ,d (double) ,s (string) or b (blob), also accepts a single value
     * @param array $a_values
     *            also accepts a single value
     * @param array $a_keys
     *            (=|<>|<|>|LIKE|IN|BETWEEN), also accepts a single value. leave empty for =
     * @throws DBException the key is invalid
     */
    public function addOr($a_fields, $a_types, $a_values, $a_keys);

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
     * @return array having
     */
    public function render();
}

interface Create
{

    public function reset();

    /**
     * Creates a table
     *
     * @param String $s_table
     *            name
     * @param Boolean $bo_dropTable
     *            to true to drop the given table before creating it
     */
    public function setTable($s_table, $bo_dropTable = false);

    /**
     * Adds a field to the create stament
     *
     * @param String $s_field
     *            field name
     * @param String $s_type
     *            field type (database type!)
     * @param int $i_length
     *            length of the field, only for length fields
     * @param String $s_default
     *            default value
     * @param String $bo_signed
     *            to true for signed value, default unsigned
     * @param String $bo_null
     *            to true for NULL allowed
     * @param String $bo_autoIncrement
     *            to true for auto increment
     */
    public function addRow($s_field, $s_type, $i_length, $s_default = '', $bo_signed = false, $bo_null = false, $bo_autoIncrement = false);

    /**
     * Adds an enum field to the create stament
     *
     * @param String $s_field
     *            field name
     * @param array $a_values
     *            values
     * @param String $s_default
     *            default value
     * @param String $bo_null
     *            to true for NULL allowed
     */
    public function addEnum($s_field, $a_values, $s_default, $bo_null = false);

    /**
     * Adds a set field to the create stament
     *
     * @param String $s_field
     *            field name
     * @param array $a_values
     *            values
     * @param String $s_default
     *            default value
     * @param String $bo_null
     *            to true for NULL allowed
     */
    public function addSet($s_field, $s_values, $s_default, $bo_null = false);

    /**
     * Adds a primary key to the given field
     *
     * @param String $s_field
     *            field name
     * @throws DBException If the field is unknown or if the primary key is allready set
     */
    public function addPrimary($s_field);

    /**
     * Adds a index to the given field
     *
     * @param String $s_field
     *            field name
     * @throws DBException If the field is unknown
     */
    public function addIndex($s_field);

    /**
     * Sets the given fields as unique
     *
     * @param String $s_field
     *            field name
     * @throws DBException If the field is unknown
     */
    public function addUnique($s_field);

    /**
     * Sets full text search on the given field
     *
     * @param String $s_field
     *            field name
     * @throws DBException If the field is unknown
     * @throws DBException If the field type is not VARCHAR and not TEXT.
     */
    public function addFullTextSearch($s_field);

    /**
     * Returns the drop table setting
     *
     * @return String drop table command. Empty string for not dropping
     */
    public function getDropTable();

    /**
     * Creates the query
     *
     * @return String query
     */
    public function render();
}
