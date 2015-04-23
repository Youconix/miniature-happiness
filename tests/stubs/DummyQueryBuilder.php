<?php
if (! interface_exists('Builder')) {
    if (! class_exists('\core\services\Service')) {
        require (NIV . 'core/services/Service.inc.php');
    }
}

class DummyQueryBuilder extends \core\services\QueryBuilder
{

    private $service_Database;

    public function __construct($service_Database)
    {
        $this->service_Database = $service_Database;
        $this->obj_builder = new DummyBuilder($this->service_Database);
    }

    /**
     * Creates the builder
     *
     * @return Builder The builder
     */
    public function createBuilder()
    {
        return $this->obj_builder;
    }
}

class DummyBuilder implements \core\services\Builder
{

    private $service_Database;

    public $i_select = 0;

    public $i_insert = 0;

    public $i_update = 0;

    public $i_delete = 0;

    /**
     * Creates the builder
     *
     * @param core\database\DAL $service_Database
     *            The DAL
     */
    public function __construct(\core\database\DAL $service_Database)
    {
        $this->service_Database = $service_Database;
    }

    /**
     * Shows the tables in the current database
     */
    public function showTables()
    {
        return $this;
    }

    /**
     * Shows the databases that the user has access to
     */
    public function showDatabases()
    {
        return $this;
    }

    /**
     * Creates a select statement
     *
     * @param String $s_table
     *            name
     * @param String $s_fields
     *            names sepperated with a ,
     */
    public function select($s_table, $s_fields)
    {
        $this->i_select ++;
        return $this;
    }

    /**
     * Creates a insert statement
     *
     * @param String $s_table
     *            name
     * @param array $a_fields
     *            names, also accepts a single value
     * @param array $a_types
     *            types : l (SQL, no parse), i (int) ,d (double) ,s (string) or b (blob), also accepts a single value
     * @param array $a_values
     *            also accepts a single value
     */
    public function insert($s_table, $a_fields, $a_types, $a_values)
    {
        $this->i_insert ++;
        return $this;
    }

    /**
     * Creates a update statement
     *
     * @param String $s_table
     *            name
     * @param array $a_fields
     *            names, also accepts a single value
     * @param array $a_types
     *            types : l (SQL, no parse), i (int) ,d (double) ,s (string) or b (blob), also accepts a single value
     * @param array $a_values
     *            also accepts a single value
     */
    public function update($s_table, $a_fields, $a_types, $a_values)
    {
        $this->i_update ++;
        return $this;
    }

    /**
     * Creates a delete statement
     *
     * @param String $s_table
     *            name
     */
    public function delete($s_table)
    {
        $this->i_delete ++;
        return $this;
    }

    /**
     * Returns the create table generation class
     *
     * @param String $s_table
     *            table name
     * @param Boolean $bo_dropTable
     *            to true to drop the given table before creating it
     * @return Create The create table generation class
     */
    public function getCreate($s_table, $bo_dropTable)
    {
        return new DummyBuilderCreate();
    }

    /**
     * Adds a inner join between 2 tables
     *
     * @param String $s_table
     *            name
     * @param String $s_field1
     *            from the first table
     * @param String $s_field2
     *            from the second table
     */
    public function innerJoin($s_table, $s_field1, $s_field2)
    {
        return $this;
    }

    /**
     * Adds a outer join between 2 tables
     *
     * @param String $s_table
     *            name
     * @param String $s_field1
     *            from the first table
     * @param String $s_field2
     *            from the second table
     */
    public function outerJoin($s_table, $s_field1, $s_field2)
    {
        return $this;
    }

    /**
     * Adds a left join between 2 tables
     *
     * @param String $s_table
     *            name
     * @param String $s_field1
     *            from the first table
     * @param String $s_field2
     *            from the second table
     */
    public function leftJoin($s_table, $s_field1, $s_field2)
    {
        return $this;
    }

    /**
     * Adds a right join between 2 tables
     *
     * @param String $s_table
     *            name
     * @param String $s_field1
     *            from the first table
     * @param String $s_field2
     *            from the second table
     */
    public function rightJoin($s_table, $s_field1, $s_field2)
    {
        return $this;
    }

    /**
     * Returns the where generation class
     *
     * @return Where where generation class
     */
    public function getWhere()
    {
        return new DummyBuilderWhere();
    }

    /**
     * Adds a limitation to the query statement
     * Only works on select, update and delete statements
     *
     * @param int $i_limit
     *            of records
     * @param int $i_offset
     *            to start from, default 0 (first record)
     */
    public function limit($i_limit, $i_offset = 0)
    {
        return $this;
    }

    /**
     * Groups the results by the given field
     *
     * @param String $s_field            
     */
    public function group($s_field)
    {
        return $this;
    }

    /**
     * Returns the having generation class
     *
     * @return Having having generation class
     */
    public function getHaving()
    {
        return new DummyBuilderWhere();
    }

    /**
     * Orders the records in the given order
     *
     * @param String $s_field1
     *            field to order on
     * @param String $s_ordering1
     *            method (ASC|DESC)
     * @param String $s_field2
     *            field to order on, optional
     * @param String $s_ordering2
     *            method (ASC|DESC), optional
     */
    public function order($s_field1, $s_ordering1 = 'ASC', $s_field2 = '', $s_ordering2 = 'ASC')
    {
        return $this;
    }

    /**
     * Return the total amount statement for the given field
     *
     * @param String $s_field
     *            field name
     * @param String $s_alias
     *            alias, default the field name
     * @return String statement
     */
    public function getSum($s_field, $s_alias = '')
    {
        return '';
    }

    /**
     * Return the maximun value statement for the given field
     *
     * @param String $s_field
     *            field name
     * @param String $s_alias
     *            alias, default the field name
     * @return String statement
     */
    public function getMaximun($s_field, $s_alias = '')
    {
        return '';
    }

    /**
     * Return the minimun value statement for the given field
     *
     * @param String $s_field
     *            field name
     * @param String $s_alias
     *            alias, default the field name
     * @return String statement
     */
    public function getMinimun($s_field, $s_alias = '')
    {
        return '';
    }

    /**
     * Return the average value statement for the given field
     *
     * @param String $s_field
     *            field name
     * @param String $s_alias
     *            alias, default the field name
     * @return String statement
     */
    public function getAverage($s_field, $s_alias = '')
    {
        return '';
    }

    /**
     * Return statement for counting the number of records on the given field
     *
     * @param String $s_field
     *            field name
     * @param String $s_alias
     *            alias, default the field name
     * @return String statement
     */
    public function getCount($s_field, $s_alias = '')
    {
        return '';
    }

    /**
     * Returns the query result
     *
     * @return DAL query result as a database object
     */
    public function getResult()
    {
        $this->service_Database->dequeueData();
        return $this->service_Database;
    }

    /**
     * Builds the query
     */
    public function render()
    {
        return $this;
    }

    /**
     * Starts a new transaction
     *
     * @throws DBException a transaction is allready active
     */
    public function transaction()
    {
        return $this;
    }

    /**
     * Commits the current transaction
     *
     * @throws DBException no transaction is active
     */
    public function commit()
    {
        return $this;
    }

    /**
     * Rolls the current transaction back
     *
     * @throws DBException no transaction is active
     */
    public function rollback()
    {
        return $this;
    }

    /**
     * Returns the DAL
     *
     * @return DAL The DAL
     */
    public function getDatabase()
    {
        return $this->service_Database;
    }
    
    /**
     * Dumps the current active database to a file
     *
     * @return string The database dump
     */
    public function dumpDatabase(){
        
    }
}

class DummyBuilderWhere implements \core\services\Where
{

    /**
     * Resets the class Where
     */
    public function reset()
    {
        return $this;
    }

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
    public function addAnd($a_fields, $a_types, $a_values, $a_keys = array() )
    {
        return $this;
    }

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
    public function addOr($a_fields, $a_types, $a_values, $a_keys)
    {
        return $this;
    }

    /**
     * Starts a sub where part
     */
    public function startSubWhere()
    {
        return $this;
    }

    /**
     * Ends a sub where part
     */
    public function endSubWhere()
    {
        return $this;
    }

    /**
     * Adds a sub query
     *
     * @param Builder $obj_builder
     *            object
     * @param String $s_field            
     * @param String $s_key
     *            (=|<>|LIKE|IN|BETWEEN)
     * @param String $s_command
     *            command (AND|OR)
     * @throws DBException the key is invalid
     * @throws DBException the command is invalid
     */
    public function addSubQuery($obj_builder, $s_field, $s_key, $s_command)
    {
        return $this;
    }

    /**
     * Renders the where
     *
     * @return array where
     */
    public function render()
    {
        return $this;
    }
}

class DummyBuilderHaving implements \core\services\Having
{

    /**
     * Resets the class Having
     */
    public function reset()
    {
        return $this;
    }

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
    public function addAnd($a_fields, $a_types, $a_values, $a_keys)
    {
        return $this;
    }

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
    public function addOr($a_fields, $a_types, $a_values, $a_keys)
    {
        return $this;
    }

    /**
     * Starts a sub having part
     */
    public function startSubHaving()
    {
        return $this;
    }

    /**
     * Ends a sub having part
     */
    public function endSubHaving()
    {
        return $this;
    }

    /**
     * Renders the having
     *
     * @return array having
     */
    public function render()
    {
        return array();
    }
}

class DummyBuilderCreate implements \core\services\Create
{

    public function reset()
    {
        return $this;
    }

    /**
     * Creates a table
     *
     * @param String $s_table
     *            name
     * @param Boolean $bo_dropTable
     *            to true to drop the given table before creating it
     */
    public function setTable($s_table, $bo_dropTable = false)
    {
        return $this;
    }

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
    public function addRow($s_field, $s_type, $i_length, $s_default = '', $bo_signed = false, $bo_null = false, $bo_autoIncrement = false)
    {
        return $this;
    }

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
    public function addEnum($s_field, $a_values, $s_default, $bo_null = false)
    {
        return $this;
    }

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
    public function addSet($s_field, $s_values, $s_default, $bo_null = false)
    {
        return $this;
    }

    /**
     * Adds a primary key to the given field
     *
     * @param String $s_field
     *            field name
     * @throws DBException If the field is unknown or if the primary key is allready set
     */
    public function addPrimary($s_field)
    {
        return $this;
    }

    /**
     * Adds a index to the given field
     *
     * @param String $s_field
     *            field name
     * @throws DBException If the field is unknown
     */
    public function addIndex($s_field)
    {
        return $this;
    }

    /**
     * Sets the given fields as unique
     *
     * @param String $s_field
     *            field name
     * @throws DBException If the field is unknown
     */
    public function addUnique($s_field)
    {
        return $this;
    }

    /**
     * Sets full text search on the given field
     *
     * @param String $s_field
     *            field name
     * @throws DBException If the field is unknown
     * @throws DBException If the field type is not VARCHAR and not TEXT.
     */
    public function addFullTextSearch($s_field)
    {
        return $this;
    }

    /**
     * Returns the drop table setting
     *
     * @return String drop table command. Empty string for not dropping
     */
    public function getDropTable()
    {
        return '';
    }

    /**
     * Creates the query
     *
     * @return String query
     */
    public function render()
    {
        return '';
    }
}