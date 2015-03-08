<?php
namespace core\services;

/**
 * Miniature-happiness is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Miniature-happiness is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Miniature-happiness. If not, see <http://www.gnu.org/licenses/>.
 *
 * Validation class
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @version 1.0
 * @since 2.0
 */

class Validation extends \core\services\Service {
    protected $a_errors;
    
    /**
     * Validates the given email address
     *
     * @param String $s_email
     *            The email address
     * @return boolean True if the email address is valid, otherwise false
     */
    public function checkEmail($s_email)
    {
        if (! filter_var($s_email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
    
        return true;
    }
    
    /**
     * Validates the given URI
     *
     * @param String $s_uri
     *            The URI
     * @return boolean True if the URI is valid, otherwise false
     */
    public function checkURI($s_uri)
    {
        if (preg_match("#^(http://|https://|ftp://|ftps://|file://)+#i", $s_uri)) {
            if (! filter_var($s_uri, FILTER_VALIDATE_URL)) {
                return false;
            }
    
            return true;
        } else {
            if (! preg_match("#^[a-z0-9\-\.]{2,255}\.+[a-z0-9\-]{2,63}#i", $s_uri)) {
                return false;
            }
            return true;
        }
    }
    
    /**
     * Validates the given dutch postal address
     *
     * @param String $s_value
     *            The postal address
     * @return boolean True if the postal address is valid, otherwise false
     */
    public function checkPostalNL($s_value)
    {
        if (trim($s_value) == "") {
            return false;
        }
        if (! preg_match("/^\d{4}\s?[a-z]{2}$/i", $s_value)) {
            return false;
        }
    
        return true;
    }
    
    /**
     * Validates the given belgium postal address
     *
     * @param String $s_value
     *            The postal address
     * @return boolean True if the postal address is valid, otherwise false
     */
    public function checkPostalBE($i_value)
    {
        if (trim($i_value) == "") {
            return false;
        }
        if (! preg_match("/^\d{4}$/", $i_value)) {
            return false;
        }
    
        if ($i_value < 1000 || $i_value > 9999) {
            return false;
        }
    
        return true;
    }
    
    /**
     * Performs the validation
     *
     * @return  boolean True if the fields are valid
     */
    public function validate($a_validation,$a_collection){
        $a_keys = array_keys($a_validation);
        $this->a_errors  = array();
        
        foreach ($a_keys as $s_key) {
            if (! array_key_exists($key, $a_collection)) {
                $this->a_errors[] = 'Error validating non existing field ' . $s_key . '.';
                continue;
            }
    
            if (array_key_exists('type', $a_validation[$s_key])) {
                $s_type = gettype($a_collection[$s_key]);
                $s_expectedType = $a_validation[$s_key]['type'];
    
                switch ($s_expectedType) {
                    case 'int':
                    case 'array':
                        if ($s_type != $s_expectedType) {
                            $this->a_errors[] = 'Invalid type for field ' . $s_field . '. Found ' . $s_type . ' but expected ' . $s_expectedType . '.';
                            continue;
                        }
                        break;
    
                    case 'float':
                        if ($s_type != 'float' && $s_type != 'double') {
                            $this->a_errors[] = 'Invalid type for field ' . $s_field . '. Found ' . $s_type . ' but expected ' . $s_expectedType . '.';
                            continue;
                        }
                        break;
                }
    
                if ($s_type == 'int' || $s_type == 'float') {
                    if (array_key_exists('min-value', $a_validation[$s_key]) && ($a_collection[$s_key] < $a_validation[$s_key]['min-value'])) {
                        $this->a_errors[] = "Field " . $s_key . " is smaller then minimun value " . $a_validation[$s_key]['min-value'] . ".";
                    }
                    if (array_key_exists('max-value', $a_validation[$s_key]) && ($a_collection[$s_key] > $a_validation[$s_key]['max-value'])) {
                        $this->a_errors[] = "Field " . $s_key . " is bigger then maximun value " . $a_validation[$s_key]['max-value'] . ".";
                    }
                }
            }
    
            if (array_key_exists('required', $a_validation[$s_key]) && (is_null($a_collection[$s_key]) || trim($a_collection[$s_key]) == '')) {
                $this->a_errors[] = 'Required field ' . $s_key . ' is not filled in.';
            }
    
            if (array_key_exists('pattern', $a_validation[$s_key]) && ! is_null($a_collection[$s_key]) && trim($a_collection[$s_key]) != '') {
                $bo_pattern = true;
                if (! in_array($a_validation[$s_key]['pattern'], array(
                    'email',
                    'url'
                )) && ! preg_match("/" . $a_validation[$s_key]['pattern'] . "/", $a_collection[$s_key])) {
                    $bo_pattern = false;
                } else
                    if (($a_validation[$s_key]['pattern'] == 'email' && ! $this->checkEmail($a_collection[$s_key]))) {
                        $bo_pattern = false;
                    } else
                        if ($a_validation[$s_key]['pattern'] == 'url' && ! $this->checkURI($a_collection[$s_key])) {
                            $bo_pattern = false;
                        }
    
                    if (! $bo_pattern) {
                        $this->a_errors[] = "Field " . $s_key . " does not match pattern " . $a_validation[$s_key]['pattern'] . ".";
                    }
            }
    
            if (array_key_exists('set', $a_validation[$s_key]) && ! in_array($a_collection[$s_key], $a_validation[$s_key]['set'])) {
                $this->a_errors[] = "Field " . $s_key . " has invalid value " . $a_collection[$s_key] . ". Only the values " . implode(', ', $a_validation[$s_key]['set']) . ' are allowed.';
            }
        }
    
        if (count($this->a_errors) > 0) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Returns the errors after validation
     * 
     * @return array    The errors
     */
    public function getErrors(){
        return $this->a_errors;
    }
}