<?php
namespace core\database;

/**
 * General database configuration layer
 * Loads the preset DAL
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014 Rachelle Scheijen
 * @author Rachelle Scheijen
 * @since 1.0
 *        @changed 09/09/12
 *       
 *        Scripthulp framework is free software: you can redistribute it and/or modify
 *        it under the terms of the GNU Lesser General Public License as published by
 *        the Free Software Foundation, either version 3 of the License, or
 *        (at your option) any later version.
 *       
 *        Scripthulp framework is distributed in the hope that it will be useful,
 *        but WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *        GNU General Public License for more details.
 *       
 *        You should have received a copy of the GNU Lesser General Public License
 *        along with Scripthulp framework. If not, see <http://www.gnu.org/licenses/>.
 */
class Query_main
{

    private $service_Settings;

    private $s_type;

    /**
     * Includes the correct DAL
     *
     * \core\services\Settings $service_Settings The settings service
     */
    public function __construct(\core\services\Settings $service_Settings)
    {
        /* databasetype */
        $this->s_type = ucfirst($service_Settings->get('settings/SQL/type'));
        $this->service_Settings = $service_Settings;
    }

    /**
     * Loads the selected DAL
     *
     * @return Object The selected DAL-object
     */
    public function loadDatabase()
    {
        require_once (NIV . 'core/database/' . $this->s_type . '.inc.php');
        
        $s_name = '\core\database\Database_' . $this->s_type;
        $obj_DAL = new $s_name($this->service_Settings);
        $obj_DAL->defaultConnect();
        
        return $obj_DAL;
    }
}

interface DAL
{

    /**
     * Destructor
     */
    public function __destruct();

    /**
     * Connects to the database with the preset login data
     */
    public function defaultConnect();

    /**
     * Checks if the given connection-data is correct
     *
     * @static
     *
     * @param array $a_data
     *            The connection data
     * @return boolean True if the data is correct, otherwise false
     */
    public static function checkLogin($s_username, $s_password, $s_database, $s_host = '127.0.0.1', $i_port = -1);

    /**
     * Connects to the set database
     *
     * @throws DBException if the connection failes
     */
    public function connection($s_username, $s_password, $s_database, $s_host = '127.0.0.1', $i_port = -1);

    /**
     * Closes the connection to the database
     */
    public function connectionEnd();

    /**
     * Returns the ID generated by a INSERT-command
     *
     * @return int The generated id
     */
    public function getId();

    /**
     * Returns numbers of rows affected generated by a UPDATE or DELETE command
     *
     * @return Int The requested id
     */
    public function affected_rows();

    /**
     * Returns or there is a connection to the database
     *
     * @return boolean True if there is a connection with the DB, false if is not
     */
    public function isConnected();

    /**
     * Excequetes the given query on the selected database
     *
     * @para String $s_query The query to excequte
     * 
     * @throws Exception when the query failes
     */
    public function query($s_query);

    /**
     * Excequetes the given query on the selected database with binded parameters
     *
     * @param string $s_query
     *            to excequte
     * @param array $a_types
     *            types : i (int) ,d (double) ,s (string) or b (blob)
     * @param array $a_values
     *            values
     * @throws Exception if the arguments are illegal
     * @throws DBException when the query failes
     */
    public function queryBinded($s_query, $a_types, $a_values);

    /**
     * Returns the number of results from the last excequeted query
     *
     * @return int The number of results
     * @throws Exception when no SELECT-query was excequeted
     */
    public function num_rows();

    /**
     * Returns the result from the query with the given row and field
     *
     * @param
     *            int The row
     * @param
     *            string The field
     * @return string The content of the requested result-field
     * @throws Exception when no SELECT-query was excequeted
     */
    public function result($i_row, $s_field);

    /**
     * Returns the results of the query in a numeric array
     *
     * @return array data-set
     * @throws Exception when no SELECT-query was excequeted
     */
    public function fetch_row();

    /**
     * Returns the results of the query in a associate and numeric array
     *
     * @return array data-set
     * @throws Exception when no SELECT-query was excequeted
     */
    public function fetch_array();

    /**
     * Returns the results of the query in a associate array
     *
     * @return array data-set
     * @throws Exception when no SELECT-query was excequeted
     */
    public function fetch_assoc();

    /**
     * Returns the results of the query in a associate array with the given field as counter-key
     *
     * @param
     *            string The field that is the counter-key
     * @return array data-set sorted on the given key
     * @throws Exception when no SELECT-query was excequeted
     */
    public function fetch_assoc_key($s_key);

    /**
     * Returns the results of the query as a object-array
     *
     * @return object data-set
     * @throws Exception when no SELECT-query was excequeted
     */
    public function fetch_object();

    /**
     * Escapes the given data for save use in queries
     *
     * @param string $s_data
     *            The data that need to be escaped
     * @return string The escaped data
     */
    public function escape_string($s_data);

    /**
     * Starts a new transaction
     *
     * @throws DBException a transaction is allready active
     */
    public function transaction();

    /**
     * Commits the current transaction
     *
     * @throws DBException no transaction is active
     */
    public function commit();

    /**
     * Rolls the current transaction back
     *
     * @throws DBException no transaction is active
     */
    public function rollback();

    /**
     * Analyses the given table
     *
     * @param string $s_table
     *            table name
     * @return boolean if the table is OK, otherwise false
     */
    public function analyse($s_table);

    /**
     * Repairs the given table
     *
     * @param string $s_table
     *            table name
     * @return boolean if the table repair succeeded, otherwise false
     */
    public function repair($s_table);

    /**
     * Optimizes the given table
     *
     * @param string $s_table
     *            table name
     */
    public function optimize($s_table);

    /**
     * Changes the active database to the given one
     *
     * @param string $s_database
     *            database
     * @throws DBException if the new databases does not exist or no access
     */
    public function useDB($s_database);

    /**
     * Checks if a database exists and if the user has access to it
     *
     * @param string $s_database            
     * @return boolean if the database exists, otherwise false
     */
    public function databaseExists($s_database);
}

?>
