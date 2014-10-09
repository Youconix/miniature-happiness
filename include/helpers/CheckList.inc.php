<?php
/** 
 * Checkbox list widget                           
 *                                                                              
 * This file is part of Scripthulp framework                                    
 *                                                                              
 * @copyright 2012,2013,2014  Rachelle Scheijen                                
 * @author    Rachelle Scheijen                                                
 * @since     1.0                                                              
 * @changed   05/08/2012
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
class Helper_CheckList extends Helper {
    protected $service_Template;
    protected $helper_HTML;
    protected $s_callback = null;
    protected $s_name;
    protected $a_values;
    
    /**
     * PHP 5 constructor
     */
    public function __construct(){
        $this->a_values = array();
        
        $this->helper_HTML  = Memory::helpers('HTML');
        $this->service_Template = Memory::services('Template');
    }
    
    /**
     * Adds a checkbox
     * 
     * @param string 	$s_name			The checkbox name
     * @param string 	$s_value		The checkbox value
     * @param string 	$s_label		The label value
     * @param boolean	$bo_checked		Set to true to set the checkbox default checked
     */
    public function addCheckbox($s_name,$s_value,$s_label='',$bo_checked = false){
        $this->a_values[$s_name]    = array($s_value,$s_label,$bo_checked);
    }
    
    /**
     * Sets the javascript callback
     * 
     * @param string $s_callback	The callback
     */
    public function setCallback($s_callback){
        $this->s_callback   = $s_callback;
    }
    
    /**
     * Generates the list
     * 
     * @return string	The list
     */
    public function generate(){
        $a_list = $this->generateList();
        
        $s_output   = '';
        foreach($a_list AS $obj_checkbox){
            $s_output .= $obj_checkbox->generateItem()."\n";
        }
        
        /* Generate widget */
        $obj_out = $this->helper_HTML->div();
        $obj_out->setID('checkList')->setClass('widget');
        $obj_out->setContent($s_output);

        return $obj_out->generateItem();
    }
    
    /**
     * Generates the checkboxes
     * 
     * @return array	The checkboxes
     */
    protected function generateList(){
        $a_list = array();
        $a_keys = array_keys($this->a_values);
        foreach($a_keys AS $s_key){
            $obj_checkbox  = $this->helper_HTML->checkbox();
            $obj_checkbox->setID($s_key);
            $obj_checkbox->setName($s_key);
            $obj_checkbox->setValue($this->a_values[$s_key][0]);
            if( !empty($this->a_values[$s_key][1]) )
                $obj_checkbox->setLabel($this->a_values[$s_key][1]);
            else 
                $obj_checkbox->setLabel($this->a_values[$s_key][0]);
                
            if( $this->a_values[$s_key][2])
                $obj_checkbox->setChecked();
                
            if( !is_null($this->s_callback) )
            	$obj_checkbox->setEvent('onclick',$this->s_callback);
            
            $a_list[]   = $obj_checkbox;
        }
        
        return $a_list;
    }
}

?>
