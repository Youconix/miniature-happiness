<?php
namespace core\models;

/** 
 * Model is the general user model class. This class is abstract and                 
 * should be inheritanced by every user model
 *                                                                              
 * This file is part of Scripthulp framework                                    
 *                                                                              
 * @copyright 2012,2013,2014  Rachelle Scheijen                                
 * @author    Rachelle Scheijen                                                
 * @since     1.0                                                              
 * @changed   04/05/2014                                                        
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
abstract class GeneralUser extends Model {
  protected $service_Hashing;
  
    /**
   * PHP5 constructor
   * 
   * @param \core\services\QueryBuilder $service_QueryBuilder The query builder
   * @parma \core\services\Security $service_Security The security service
   * @param \core\services\Hashing $service_Hashing   The hashing service
   */
  public function __construct(\core\services\QueryBuilder $service_QueryBuilder,\core\services\Security $service_Security,\core\services\Hashing $service_Hashing){
    parent::__construct($service_QueryBuilder,$service_Security);
    $this->service_Hashing = $service_Hashing;
  }
  
    /**
     * Hashes the given password with the set salt and sha1
     *
     * @param      string $s_password  The password
     * @param		string	$s_username	The username
     * @param   bool  $bo_legancy   Set to true to hash on the V1 way
     * @return     string  The hashed password
     */
    public function hashPassword($s_password,$s_username,$bo_legancy = false){
        if( !$bo_legancy ){
          return $this->service_Hashing->hashUserPassword($s_password, $s_username);
        }
        
        $service_XmlSettings  = \core\Memory::services('XmlSettings');
        
        $s_salt = $service_XmlSettings->get('settings/main/salt');
        
        return sha1(substr(md5($s_username),5,30).$s_password.$s_salt);
    }
}

?>
