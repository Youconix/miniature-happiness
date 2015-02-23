<?php
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
 * Framework upgrade file
 * 
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author    Rachelle Scheijen
 * @since     1.0
 */
define('NIV', '../');
define('PROCESS', '1');

require (NIV . 'admin/SettingsMain.php');

class Upgrade extends SettingsMain
{

    /**
     * PHP 5 constructor
     */
    public function __construct()
    {
        $this->init();
        
        $this->performUpgrade();
        
        header('location: ' . NIV . 'index.php');
        exit();
    }

    /**
     * Inits the class Install
     */
    private function init()
    {
        if (! defined('DATA_DIR')) {
            define('DATA_DIR', NIV . 'admin/data/');
        }
        
        if (! file_exists(DATA_DIR . '/settings/settings.xml')) {
            header('location: ' . NIV . 'install/index.php');
            exit();
        }
        
        require (NIV . 'include/Memory.php');
        Memory::startUp();
    }

    private function performUpgrade()
    {
        $service_File = Memory::services('File');
        $service_QueryBuilder = Memory::services('QueryBuilder')->createBuilder();
        
        try {
            $a_countries = explode("\n", $service_File->readFile(NIV . 'install/countries.csv'));
            $a_nationalities = explode("\n", $service_File->readFile(NIV . 'install/nationality.csv'));
        } catch (IOException $e) {
            echo ("Reading upgrade files countries.cvs and nationality.csv failed");
            exit();
        }
        
        try {
            $obj_create = $service_QueryBuilder->transaction();
            
            /* Countries */
            $obj_create = $service_QueryBuilder->getCreate('countries', true);
            $obj_create->addRow('id', 'int', 11, '', false, false, true);
            $obj_create->addRow('country', 'varchar', 250);
            $obj_create->addPrimary('id');
            $service_QueryBuilder->getResult();
            
            foreach ($a_countries as $a_country) {
                $a_country = explode(',', $a_country);
                $i_id = str_replace('"', '', $a_country[0]);
                $s_country = str_replace('"', '', $a_country[1]);
                
                $service_QueryBuilder->insert('countries', array(
                    'id',
                    'country'
                ), array(
                    'i',
                    's'
                ), array(
                    $i_id,
                    $s_country
                ))->getResult();
            }
            
            /* Nationalities */
            $obj_create = $service_QueryBuilder->getCreate('nationalities', true);
            $obj_create->addRow('id', 'int', 11, '', false, false, true);
            $obj_create->addRow('nationality', 'varchar', 250);
            $obj_create->addPrimary('id');
            $service_QueryBuilder->getResult();
            
            foreach ($a_nationalities as $a_nationality) {
                $a_nationality = explode(',', $a_nationality);
                $i_id = str_replace('"', '', $a_nationality[0]);
                $s_nationality = str_replace('"', '', $a_nationality[1]);
                
                $service_QueryBuilder->insert('nationalities', array(
                    'id',
                    'nationality'
                ), array(
                    'i',
                    's'
                ), array(
                    $i_id,
                    $s_nationality
                ))->getResult();
            }
            
            $service_QueryBuilder->commit();
            
            return true;
        } catch (DBException $e) {
            $service_QueryBuilder->rollback();
            return false;
        }
    }
}

$obj_Install = new Upgrade();
unset($obj_Install);