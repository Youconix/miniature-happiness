<?php
use core\class_alias;

/**
 * Shared settings file
 * This file is shared between the installer and the control panel
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014 Rachelle Scheijen
 * @author Rachelle Scheijen
 * @since 1.0
 *        @changed 11/12/12
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
class SettingsMain
{

    /**
     * Generates a select list
     *
     * @param array $a_data            
     * @param string $s_default
     *            value
     * @return string list
     */
    public function generateList($a_data, $s_default)
    {
        $s_output = '';
        foreach ($a_data as $s_item) {
            $s_selected = '';
            if ($s_default == $s_item)
                $s_selected = 'selected="selected"';
            
            $s_output .= '<option ' . $s_selected . ' value="' . $s_item . '">' . $s_item . '</option>';
        }
        return $s_output;
    }

    /**
     * Returns the available languages
     *
     * @return array languages
     */
    public function getLanguages()
    {
        $a_languages = array();
        
        $s_dir = NIV . 'include/language';
        $s_handle = @opendir($s_dir);
        if ($s_handle === false)
            return $a_languages;
            
            /* read all language files */
        while (false !== ($s_file = readdir($s_handle))) {
            if (($s_file == '.') || ($s_file == '..') || (substr($s_file, - 1) == "~") || $s_file == 'Install.php')
                continue;
                
                /* check of $file is a directory */
            if (is_dir($s_dir . '/' . $s_file)) {
                if (strpos($s_file, '_') === false) {
                    continue;
                }
            } else 
                if (stripos($s_file, ".lang") === false) {
                    continue;
                }
            
            $a_languages[] = str_replace(array(
                'language_',
                '.lang'
            ), array(
                '',
                ''
            ), $s_file);
        }
        
        /* close directory */
        closedir($s_handle);
        
        return $a_languages;
    }

    /**
     * Returns the available template directories
     *
     * @return array directories
     */
    public function getTemplates()
    {
        $a_templates = array();
        
        $s_handle = @opendir(NIV . 'styles');
        if ($s_handle === false)
            return $a_templates;
            
            /* read all language files */
        while (false !== ($s_file = readdir($s_handle))) {
            if (($s_file == '.') || ($s_file == '..') || (substr($s_file, - 1) == "~"))
                continue;
                
                /* check of $file is a directory */
            if (! is_dir(NIV . 'styles/' . $s_file))
                continue;
            
            $a_templates[] = $s_file;
        }
        
        /* close directory */
        closedir($s_handle);
        
        return $a_templates;
    }

    /**
     * Returns the available database access layers
     *
     * @return array DALs
     */
    public function getDatabases()
    {
        $a_databases = array();
        
        $s_handle = @opendir(NIV . 'include/database');
        if ($s_handle === false)
            return $a_databases;
            
            /* read all language files */
        while (false !== ($s_file = readdir($s_handle))) {
            if (($s_file == '.') || ($s_file == '..') || (substr($s_file, - 1) == "~"))
                continue;
                
                /* check of $file is a directory */
            if (is_dir(NIV . 'templates/' . $s_file))
                continue;
            
            if (stripos($s_file, 'binded') !== false)
                continue;
            
            if (stripos($s_file, 'builder') !== false)
                continue;
            
            $a_databases[] = str_replace('.inc.php', '', $s_file);
        }
        
        /* close directory */
        closedir($s_handle);
        
        return $a_databases;
    }

    /**
     * Returns the available FTP types (SFTP|FTP|FTP-S)
     *
     * @return array ftp types
     */
    public function getFtpTypes()
    {
        $a_ftp = array();
        
        if (function_exists('ftp_connect'))
            $a_ftp[] = 'FTP';
        if (function_exists('ftp_ssl_connect'))
            $a_ftp[] = 'FTP-S';
        if (function_exists('ssh2_sftp'))
            $a_ftp[] = 'S-FTP';
        
        return $a_ftp;
    }

    /**
     * Checks if the database connection data is correct
     *
     * @param array $a_data
     *            connection data
     * @return boolean if the data is correct
     */
    public function checkDatabase($a_data)
    {
        require_once (NIV . 'include/services/Service.inc.php');
        require_once (NIV . 'include/services/Database.inc.php');
        
        switch ($a_data['databaseType']) {
            case 'Mysqli':
                require_once (NIV . 'include/database/Mysqli.inc.php');
                $bo_oke = \core\database\Database_Mysqli::checkLogin($a_data['sqlUsername'], $a_data['sqlPassword'], $a_data['sqlDatabase'], $a_data['sqlHost'], $a_data['sqlPort']);
                break;
            
            case 'PostgreSql':
                require_once (NIV . 'include/database/PostgreSql.inc.php');
                $bo_oke = \core\database\Database_PostgreSql::checkLogin($a_data['sqlUsername'], $a_data['sqlPassword'], $a_data['sqlDatabase'], $a_data['sqlHost'], $a_data['sqlPort']);
                break;
        }
        
        return $bo_oke;
    }

    /**
     * Checks if the FTP connection data is correct
     *
     * @param array $a_data
     *            connection data
     * @return boolean if the data is correct
     */
    public function checkFTP($a_data)
    {
        require_once (NIV . 'include/services/Service.inc.php');
        require_once (NIV . 'include/services/FTP.inc.php');
        
        $obj_FTP = new Service_FTP(false);
        $i_port = 21;
        if ($a_data['ftpPort'] != '')
            $i_port = $a_data['ftpPort'];
        return $obj_FTP->checkLogin($a_data['ftpUsername'], $a_data['ftpPassword'], $a_data['ftpHost'], $i_port = 21, $a_data['ftpType']);
    }

    public function checkLDAP($s_host, $i_port)
    {
        require_once (NIV . 'include/services/Service.inc.php');
        require_once (NIV . 'include/services/LDAP.inc.php');
        
        $obj_LDAP = new Service_LDAP();
        return $obj_LDAP->checkConnection($s_host, $i_port);
    }

    public function checkSMTP($s_host, $i_port, $s_username, $s_password)
    {
        require_once (NIV . 'include/services/Service.inc.php');
        require_once (NIV . 'include/mailer/MailWrapper.inc.php');
        $obj_MailWrapper = new MailWrapper();
        
        return $obj_MailWrapper->checkSmtpDetails($s_host, $i_port, $s_username, $s_password);
    }
}

?>
