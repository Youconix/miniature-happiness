<?php
namespace core\database;

/**
 * Database connection layer for PostgreSQL
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014 Rachelle Scheijen
 * @author Rachelle Scheijen
 * @since 1.0
 *
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
class Database_PostgreSql implements DAL
{

    private $obj_connection;

    private $obj_query;

    private $bo_connection;

    private $s_lastDatabase;

    private $i_id;

    private $i_affected_rows;

    private $bo_transaction = false;

    /**
     * Destructor
     */
    public function __destruct()
    {
        if ($this->bo_connection) {
            $this->connectionEnd();
        }
        
        $this->obj_connection = null;
        $this->obj_query = null;
        $this->bo_connection = null;
        $this->s_lastDatabase = null;
        $this->i_id = null;
        $this->i_affected_rows = null;
        $this->bo_transaction = null;
    }

    /**
     * Connects to the database with the preset login data
     */
    public function defaultConnect()
    {
        $service_XmlSettings = Memory::services('XmlSettings');
        
        $this->bo_connection = false;
        
        $this->connection($service_XmlSettings->get('settings/SQL/PostgreSQL/username'), $service_XmlSettings->get('settings/SQL/PostgreSQL/password'), $service_XmlSettings->get('settings/SQL/PostgreSQL/database'), $service_XmlSettings->get('settings/SQL/PostgreSQL/host'), $service_XmlSettings->get('settings/SQL/PostgreSQL/port'));
        
        $this->reset();
    }

    /**
     * Resets the internal query cache.
     */
    public function reset()
    {
        if (! is_null($this->obj_query))
            pg_free_result($this->obj_query);
        
        $this->obj_query = null;
        $this->i_id = - 1;
        $this->i_affected_rows = - 1;
    }

    /**
     * Returns numbers of rows affected generated by a UPDATE or DELETE command
     *
     * @return int The requested id
     */
    public function affected_rows()
    {
        return $this->i_affected_rows;
    }

    /**
     * Checks if the given connection-data is correct
     *
     * @static
     *
     * @param String $s_username
     *            The username
     * @param String $s_password
     *            The password
     * @param String $s_database
     *            The database
     * @param String $s_host
     *            The host name, default 127.0.0.1 (localhost)
     * @param int $i_port
     *            The port
     * @return Boolean True if the data is correct, otherwise false
     */
    public static function checkLogin($s_username, $s_password, $s_database, $s_host = '127.0.0.1', $i_port = -1)
    {
        if ($i_port == - 1)
            $i_port = '';
        
        try {
            /* connect to the database */
            $s_res = pg_connect("host=" . $s_host . " port=" . $s_port . " dbname=" . $s_database . " user=" . $s_username . " password=" . $s_password);
            
            if (! $s_res) {
                return false;
            }
            
            pg_close($s_res);
            
            return true;
        } catch (Exception $exception) {
            /* Error connecting */
            return false;
        }
    }

    /**
     * Connects to the set database
     *
     * @param String $s_username
     *            The username
     * @param String $s_password
     *            The password
     * @param String $s_database
     *            The database
     * @param String $s_host
     *            The host name, default 127.0.0.1 (localhost)
     * @param int $i_port
     *            The port
     * @throws DBException If connection to the database failed
     */
    public function connection($s_username, $s_password, $s_database, $s_host = '127.0.0.1', $i_port = -1)
    {
        if ($this->bo_connection)
            return;
        
        if ($i_port == - 1)
            $i_port = '';
        
        $s_res = pg_connect("host=" . $s_host . " port=" . $s_port . " dbname=" . $s_database . " user=" . $s_username . " password=" . $s_password);
        if ($s_res == false) {
            /* Error connecting */
            throw new DBException("Error connection to database " . $s_database . '. Check the connection-settings');
            $this->bo_conntection = false;
        }
        
        $this->s_lastDatabase = $s_database;
        $this->bo_connection = true;
    }

    /**
     * Closes the connection to the mysql database
     */
    public function connectionEnd()
    {
        if ($this->bo_connection) {
            pg_close();
            $this->bo_connection = false;
        }
    }

    /**
     * Escapes the given data for save use in queries
     *
     * @param String $s_data
     *            The data that need to be escaped
     * @return String The escaped data
     */
    public function escape_string($s_data)
    {
        $s_data = htmlentities($s_data, ENT_QUOTES);
        
        return pg_escape_string($s_data);
    }

    /**
     * Returns the results of the query in a numeric array
     *
     * @return array data-set
     * @throws DBException when no SELECT-query was excequeted
     */
    public function fetch_row()
    {
        if (is_null($this->obj_query))
            throw new DBException("Trying to get data on a non-SELECT-query");
        
        $a_temp = array();
        while ($a_res = pg_fetch_row($this->obj_query)) {
            $a_temp[] = $a_res;
        }
        
        return $a_temp;
    }

    /**
     * Returns the results of the query in a associate and numeric array
     *
     * @return array data-set
     * @throws DBException when no SELECT-query was excequeted
     */
    public function fetch_array()
    {
        if (is_null($this->obj_query))
            throw new DBException("Trying to get data on a non-SELECT-query");
        
        $a_ret = array();
        for ($i = 0; $a_arr = pg_fetch_array($s_result, $i, PGSQL_ASSOC); $i ++) {
            $a_ret = $a_arr[$i];
        }
        
        return $a_ret;
    }

    /**
     * Returns the results of the query in a associate array
     *
     * @return array data-set
     * @throws DBException when no SELECT-query was excequeted
     */
    public function fetch_assoc()
    {
        if (is_null($this->obj_query))
            throw new DBException("Trying to get data on a non-SELECT-query");
        
        $a_temp = array();
        while ($a_res = pg_fetch_assoc($this->obj_query)) {
            $a_temp[] = $a_res;
        }
        
        return $a_temp;
    }

    /**
     * Returns the results of the query in a associate array with the given field as counter-key
     *
     * @param
     *            String The field that is the counter-key
     * @return array data-set sorted on the given key
     * @throws DBException when no SELECT-query was excequeted
     */
    public function fetch_assoc_key($s_key)
    {
        if (is_null($this->obj_query))
            throw new DBException("Trying to get data on a non-SELECT-query");
        
        $a_temp = array();
        while ($a_res = pg_fetch_assoc($this->obj_query)) {
            $a_temp[$a_res[$s_key]] = $a_res;
        }
        
        return $a_temp;
    }

    /**
     * Returns the results of the query as a object-array
     *
     * @return Object data-set
     * @throws Exception when no SELECT-query was excequeted
     */
    public function fetch_object()
    {
        if (is_null($this->obj_query))
            throw new DBException("Trying to get data on a non-SELECT-query");
        
        $a_temp = array();
        while ($obj_res = pg_fetch_object($this->obj_query)) {
            $a_temp[] = $a_obj;
        }
        
        return $a_temp;
    }

    /**
     * Returns the ID generated by a INSERT-command
     *
     * @return int The generated id
     */
    public function getId()
    {
        return $this->i_id;
    }

    /**
     * Returns or there is a connection to the database
     *
     * @return Boolean True if there is a connection with the DB, false if is not
     */
    public function isConnected()
    {
        return $this->bo_connection;
    }

    /**
     * Returns the number of results from the last excequeted query
     *
     * @return int The number of results
     * @throws DBException when no SELECT-query was excequeted
     */
    public function num_rows()
    {
        if (is_null($this->obj_query))
            throw new DBException("Trying to count the numbers of results on a non-SELECT-query");
        
        return pg_numrows($this->obj_query);
    }

    /**
     * Excequetes the given query on the selected database
     *
     * @para String $s_query The query to excequte
     *
     * @throws DBException if no connection to the database exists
     * @throws DBException in case of a SQL error
     */
    public function query($s_query)
    {
        if (! $this->bo_connection)
            throw new DBException("No connection to the database");
        
        $this->reset();
        
        $qry_sql = pg_query($s_query);
        
        if (! $qry_sql) {
            throw new DBException("Query failed : " . pg_last_error() . '.\n' . $s_query);
        }
        
        $this->parseResult($s_query, $qry_sql);
    }

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
    public function queryBinded($s_query, $a_types, $a_values)
    {
        if (is_null($s_query) || empty($s_query)) {
            throw new Exception("Illegal query call " . $s_query);
        }
        
        $this->reset();
        
        $result = pg_prepare($this->obj_connection, "postgreSQL_query", $s_query);
        if ($result === false) {
            throw new DBException("Query failed : " . pg_last_error() . '.\n' . $s_query);
        }
        
        $qry_sql = pg_execute($this->obj_connection, "postgreSQL_query", $a_values);
        
        $this->parseResult($s_query, $qry_sql);
    }

    /**
     * Parses the query result
     *
     * @param String $s_query
     *            query stirng
     * @param Resource $qry_sql
     *            query result
     */
    private function parseResult($s_query, $qry_sql)
    {
        $s_command = strtoupper(trim(substr($s_query, 0, strpos($s_query, ' '))));
        if ($s_command == 'SELECT' || $s_command == 'SHOW' || $s_command == 'ANALYZE') {
            $this->obj_query = $qry_sql;
        } else 
            if ($s_command == 'INSERT') {
                $insert_row = pg_fetch_row();
                $this->i_id = $insert_row[0];
            } else 
                if ($s_command == 'UPDATE' || $s_command == 'DELETE') {
                    $this->i_affected_rows = pg_affected_rows();
                }
    }

    /**
     * Returns the result from the query with the given row and field
     *
     * @param
     *            int The row
     * @param
     *            String The field
     * @return String The content of the requested result-field
     * @throws DBException if no SELECT-query was excequeted
     */
    public function result($i_row, $s_field)
    {
        if (is_null($this->obj_query))
            throw new DBException("Trying to get data on a non-SELECT-query");
        
        return pg_fetch_result($this->obj_query, $i_row, $s_field);
    }

    /**
     * Starts a new transaction
     *
     * @throws DBException a transaction is allready active
     */
    public function transaction()
    {
        if ($this->bo_transaction) {
            throw new DBException("Can not start new transaction. Call commit() or rollback() first.");
        }
        
        $this->query("BEGIN");
        $this->bo_transaction = true;
    }

    /**
     * Commits the current transaction
     *
     * @throws DBException no transaction is active
     */
    public function commit()
    {
        if (! $this->bo_transaction) {
            throw new DBException("Can not commit transaction. Call transaction() first.");
        }
        
        $this->query("COMMIT");
        $this->bo_transaction = false;
    }

    /**
     * Rolls the current transaction back
     *
     * @throws DBException no transaction is active
     */
    public function rollback()
    {
        if (! $this->bo_transaction) {
            throw new DBException("Can not rollback transaction. Call transaction() first.");
        }
        
        $this->query("ROLLBACK");
        $this->bo_transaction = false;
    }

    public function analyse($s_table)
    {}

    public function databaseExists($s_database)
    {}

    public function optimize($s_table)
    {}

    public function repair($s_table)
    {}

    public function useDB($s_database)
    {}
}
?>
