<?php
/** 
 * Radio group list widget
 * All radio buttons in a group have the same name                          
 *                                                                              
 * This file is part of Scripthulp framework                                    
 *                                                                              
 * @copyright 2012,2013,2014  Rachelle Scheijen                                
 * @author    Rachelle Scheijen                                                
 * @since     1.0                                                              
 * @changed   05/08/2012
 * @see		  include/helpers/RadioList.inc.php
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
require_once(NIV.'include/helpers/RadioList.inc.php');

class Helper_UniqueRadioList extends Helper_RadioList {
    private $s_listName;
    
    /**
     * PHP 5 constructor
     */
    public function __construct(){
        parent::__construct();
        
        $this->s_name       = 'uniqueRadioListName';
        $this->s_listName   = $this->s_name;
    }
    
    /**
     * Adds a radio button
     * 
     * @param string 	$s_value		The value
     * @param string 	$s_label		The label value
     * @param boolean	$bo_checked		Set to true to set the radio button default checked
     */
    public function addRadio($s_value,$s_label='',$bo_checked = false){
        $this->a_values[]    = array($s_value,$s_label,$bo_checked);
    }
    
    /**
     * Sets the name of the radio buttons
     * 
     * @param string $s_name	The name
     */
    public function setListName($s_name){
        $this->s_listName   = $s_name;
    }
    
	/**
     * Generates the radio buttons
     * 
     * @return array	The radio buttons
     */
    protected function generateList(){
    	$a_list = array();
    	
    	$i=1;
    	foreach($this->a_values AS $a_radio){
            $obj_radio  = $this->helper_HTML->radio();
            $obj_radio->setID($this->s_name.'_'.$i);
            $obj_radio->setName($this->s_name);
            $obj_radio->setValue($a_radio[0]);
            $obj_radio->setLabel($a_radio[1]);
            if( $a_radio[2])
                $obj_radio->setChecked();
                
            if( !is_null($this->s_callback) )
            	$obj_radio->setEvent('onclick',$this->s_callback);
            
            $a_list[]   = $obj_radio;            
            $i++;
        }
        
        return $a_list;
    }
}
?>