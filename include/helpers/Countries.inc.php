<?php
/**
 * Countries list widget
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014  Rachelle Scheijen
 * @author    Rachelle Scheijen
 * @since     1.0
 * @changed   23/11/2013
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
class Helper_Countries extends Helper {
	private $a_countries = array();
	
	/**
	 * Creates the country helper
	 */
	public function __construct(){
		$service_QueryBuilder = Memory::services('QueryBuilder')->createBuilder();
		
		$service_QueryBuilder->select("countries","*");
		$service_Database = $service_QueryBuilder->getResult();
		if( $service_Database->num_rows() > 0 ){
			$a_data = $service_Database->fetch_assoc_key('country');
			ksort($a_data,SORT_STRING);
			
			foreach($a_data AS $a_item){
				$this->a_countries[$a_item['id']] = $a_item;
			}
		}
	}
	
	/**
	 * Returns the country
	 * 
	 * @param int $i_id		The country ID
	 * @return array	The country
	 * @throws IllegalArgumentException		If the ID does not exist
	 */
	public function getItem($i_id){
		if( !array_key_exists($i_id,$this->a_countries) ){
			throw new IllegalArgumentException("Call to unknown country with id ".$i_id.'.');
		}
		
		return $this->a_countries[$i_id];
	}
	
	/**
	 * Returns the countries sorted on name
	 * 
	 * @return array	The countries
	 */
	public function getItems(){
		return $this->a_countries;
	}
	
	/**
	 * Generates the selection list
	 *
	 * @param string $s_field		The list name
	 * @param string $s_id			The list id
	 * @param string $s_default		The default value, optional
	 * @return string		The list
	 */
	public function getList($s_field,$s_id,$i_default){
		$obj_Select = Memory::helpers('HTML')->select($s_field);
		$obj_Select->setID($s_id);
		
		foreach($this->a_countries AS $a_country){
			( $a_country['id'] == $i_default ) ? $bo_selected = true : $bo_selected = false;
				
			$obj_Select->setOption($a_country['country'], $bo_selected, $a_country['id']);
		}
		
		return $obj_Select->generateItem();
	}
}
?>