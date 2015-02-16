<?php
namespace core\models;

/**
 * Model is the general model class.
 * This class is abstract and
 * should be inheritanced by every model.
 * This class handles setting up the database connection
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014 Rachelle Scheijen
 * @author Rachelle Scheijen
 * @since 1.0
 *        @changed 13/05/13
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
abstract class Model
{

    protected $service_Security;

    protected $service_Database;

    protected $service_QueryBuilder;

    protected $a_validation = array();

    protected $bo_throwError = true;

    /**
     * PHP5 constructor
     *
     * @param \core\services\QueryBuilder $service_QueryBuilder
     *            The query builder
     * @param \core\services\Security $service_Security
     *            The security service
     */
    public function __construct(\core\services\QueryBuilder $service_QueryBuilder, \core\services\Security $service_Security)
    {
        $this->service_QueryBuilder = $service_QueryBuilder->createBuilder();
        $this->service_Database = $this->service_QueryBuilder->getDatabase();
        $this->service_Security = $service_Security;
    }

    /**
     * Clones the model
     *
     * @return Model The cloned model
     */
    public function cloneModel()
    {
        return clone $this;
    }

    /**
     * Validates the model
     *
     * @return Boolean if the model is valid, otherwise false
     */
    public function validate()
    {
        try {
            $this->performValidation();
            return true;
        } catch (\ValidationException $e) {
            return false;
        }
    }

    /**
     * Performs the model validation
     *
     * @throws ValidationException the model is invalid
     */
    protected function performValidation()
    {
        $a_error = array();
        
        $a_keys = array_keys($this->a_validation);
        foreach ($a_keys as $s_key) {
            if (! isset($this->$s_key)) {
                $a_error[] = 'Error validating non existing field ' . $s_key . '.';
                continue;
            }
            
            if (array_key_exists('type', $this->a_validation[$s_key])) {
                $s_type = gettype($this->$s_key);
                $s_expectedType = $this->a_validation[$s_key]['type'];
                
                switch ($s_expectedType) {
                    case 'int':
                    case 'array':
                        if ($s_type != $s_expectedType) {
                            $a_error[] = 'Invalid type for field ' . $s_field . '. Found ' . $s_type . ' but expected ' . $s_expectedType . '.';
                            continue;
                        }
                        break;
                    
                    case 'float':
                        if ($s_type != 'float' && $s_type != 'double') {
                            $a_error[] = 'Invalid type for field ' . $s_field . '. Found ' . $s_type . ' but expected ' . $s_expectedType . '.';
                            continue;
                        }
                        break;
                }
                
                if ($s_type == 'int' || $s_type == 'float') {
                    if (array_key_exists('min-value', $this->a_validation[$s_key]) && ($this->$s_key < $this->a_validation[$s_key]['min-value'])) {
                        $a_error[] = "Field " . $s_key . " is smaller then minimun value " . $this->a_validation[$s_key]['min-value'] . ".";
                    }
                    if (array_key_exists('max-value', $this->a_validation[$s_key]) && ($this->$s_key > $this->a_validation[$s_key]['max-value'])) {
                        $a_error[] = "Field " . $s_key . " is bigger then maximun value " . $this->a_validation[$s_key]['max-value'] . ".";
                    }
                }
            }
            
            if (array_key_exists('required', $this->a_validation[$s_key]) && (is_null($this->$s_key) || trim($this->$s_key) == '')) {
                $a_error[] = 'Required field ' . $s_key . ' is not filled in.';
            }
            
            if (array_key_exists('pattern', $this->a_validation[$s_key]) && ! is_null($this->$s_key) && trim($this->$s_key) != '') {
                $bo_pattern = true;
                if (! in_array($this->a_validation[$s_key]['pattern'], array(
                    'email',
                    'url'
                )) && ! preg_match("/" . $this->a_validation[$s_key]['pattern'] . "/", $this->$s_key)) {
                    $bo_pattern = false;
                } else 
                    if (($this->a_validation[$s_key]['pattern'] == 'email' && ! $this->service_Security->checkEmail($this->$s_key))) {
                        $bo_pattern = false;
                    } else 
                        if ($this->a_validation[$s_key]['pattern'] == 'url' && ! $this->service_Security->checkURI($this->$s_key)) {
                            $bo_pattern = false;
                        }
                
                if (! $bo_pattern) {
                    $a_error[] = "Field " . $s_key . " does not match pattern " . $this->a_validation[$s_key]['pattern'] . ".";
                }
            }
            
            if (array_key_exists('set', $this->a_validation[$s_key]) && ! in_array($this->$s_key, $this->a_validation[$s_key]['set'])) {
                $a_error[] = "Field " . $s_key . " has invalid value " . $this->$s_key . ". Only the values " . implode(', ', $this->a_validation[$s_key]['set']) . ' are allowed.';
            }
        }
        
        if (! $this->bo_throwError) {
            return $a_error;
        }
        
        if (count($a_error) > 0) {
            throw new \ValidationException("Error validating : \n" . implode("\n", $a_error));
        }
    }
}
?>
