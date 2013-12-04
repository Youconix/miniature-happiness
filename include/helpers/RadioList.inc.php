<?php
/** 
 * Radio list widget                           
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
class Helper_RadioList extends Helper {
    protected $service_Template;
    protected $helper_HTML;
    protected $s_callback = 'null';
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
     * Adds a radio button
     * 
   	 * @param string 	$s_name			The name
     * @param string 	$s_value		The value
     * @param string 	$s_label		The label value
     * @param boolean	$bo_checked		Set to true to set the radio button default checked
     */
    public function addRadio($s_name,$s_value,$s_label='',$bo_checked = false){
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
        foreach($a_list AS $obj_radio){
            $s_output .= $obj_radio->generateItem()."\n";
        }
        
        /* Generate widget */
        $obj_out = $this->helper_HTML->div();
        $obj_out->setID('radioList')->setClass('widget');
        $obj_out->setContent($s_output);

        return $obj_out->generateItem();
    }
    
    /**
     * Generates the radio buttons
     * 
     * @return array	The radio buttons
     */
    protected function generateList(){
    	$a_list = array();
        $a_keys = array_keys($this->a_values);
        foreach($a_keys AS $s_key){
            $obj_radio  = $this->helper_HTML->radio();
            $obj_radio->setID($s_key);
            $obj_radio->setName($s_key);
            $obj_radio->setValue($this->a_values[$s_key][0]);
            $obj_radio->setLabel($this->a_values[$s_key][1]);
            if( $this->a_values[$s_key][2])
                $obj_radio->setChecked();
                
            if( !is_null($this->s_callback) )
            	$obj_radio->setEvent('onclick',$this->s_callback);
            
            $a_list[]   = $obj_radio;
        }
        
        return $a_list;
    }
}

?>
