<?php
/** 
 * Shared settings file
 * This file is shared between the installer and the control panel                           
 *                                                                              
 * This file is part of Scripthulp framework                                    
 *                                                                              
 * @copyright 2012,2013,2014  Rachelle Scheijen                                
 * @author    Rachelle Scheijen                                                
 * @since     1.0                                                              
 * @changed   11/12/12
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
class SettingsMain {
	/**
	 * Generates a select list
	 * 
	 * @param array		$a_data			The items
	 * @param string	$s_default		The default value
	 * @return string	The list
	 */
    public function generateList($a_data,$s_default){
        $s_output   = '';
        foreach($a_data AS $s_item){
            $s_selected = '';
            if( $s_default == $s_item) $s_selected = 'selected="selected"';
            
            $s_output .= '<option '.$s_selected.' value="'.$s_item.'">'.$s_item.'</option>';
        }
        return $s_output;
    }
    
    /**
     * Returns the available languages
     * 
     * @return array	The languages
     */
    public function getLanguages(){
        $a_languages    = array();
        
        $s_handle = @opendir(NIV.'include/language');
        if( $s_handle === false )   return $a_languages;

        /* read all language files */
        while( false !== ($s_file = readdir($s_handle)) ){
            if( ($s_file == '.') || ($s_file == '..') || (substr($s_file,-1) == "~") )  continue;

            /* check of $file is a directory */
            if( is_dir($s_file) || stripos($s_file, ".lang") === false )  continue;
            
            $a_languages[]  = str_replace(array('language_','.lang'),array('',''),$s_file);
        }

        /* close directory */
        closedir( $s_handle );
        
        return $a_languages;
    }
    
    /**
     * Returns the available template directories
     *
     * @return array	The directories
     */
    public function getTemplates(){
        $a_templates    = array();
        
        $s_handle = @opendir(NIV.'styles');
        if( $s_handle === false )   return $a_templates;

        /* read all language files */
        while( false !== ($s_file = readdir($s_handle)) ){
            if( ($s_file == '.') || ($s_file == '..') || (substr($s_file,-1) == "~") )  continue;

            /* check of $file is a directory */
            if( !is_dir(NIV.'styles/'.$s_file) )  continue;
            
            $a_templates[]  = $s_file;
        }

        /* close directory */
        closedir( $s_handle );
        
        return $a_templates;
    }
    
    /**
     * Returns the available database access layers
     *
     * @return array	The DALs
     */
    public function getDatabases(){
        $a_databases    = array();
        
        $s_handle = @opendir(NIV.'include/database');
        if( $s_handle === false )   return $a_databases;

        /* read all language files */
        while( false !== ($s_file = readdir($s_handle)) ){
            if( ($s_file == '.') || ($s_file == '..') || (substr($s_file,-1) == "~") )  continue;

            /* check of $file is a directory */
            if( is_dir(NIV.'templates/'.$s_file) )  continue;
            
            if(stripos($s_file,'binded') !== false )	continue;
            
            if(stripos($s_file,'builder') !== false )	continue;
            
            $a_databases[]  = str_replace('.inc.php','',$s_file);
        }

        /* close directory */
        closedir( $s_handle );
        
        return $a_databases;
    }
    
    /**
     * Returns the available FTP types (SFTP|FTP|FTP-S)
     *
     * @return array	The ftp types
     */
    public function getFtpTypes(){
        $a_ftp  = array();
        
        if( function_exists('ftp_connect') ) $a_ftp[]    = 'FTP';
        if( function_exists('ftp_ssl_connect') ) $a_ftp[]    = 'FTP-S';
        if( function_exists('ssh2_sftp') ) $a_ftp[]    = 'S-FTP';
        
        return $a_ftp;
    }
    
    /**
     * Checks if the database connection data is correct
     * 
     * @param array $a_data	The connection data
     * @return boolean	True if the data is correct
     */
    public function checkDatabase($a_data){
        require_once(NIV.'include/services/Service.inc.php');
        require_once(NIV.'include/services/Database.inc.php');
        
        switch($a_data['databaseType']){
            case 'Mysql':
                require_once(NIV.'include/database/Mysql.inc.php');
                $obj_database   = new Database_Mysqli();
            break;
            
            case 'Mysqli':
                require_once(NIV.'include/database/Mysqli.inc.php');
                $obj_database   = new Database_Mysqli();
            break;
        
            case 'PostgreSql' :
                require_once(NIV.'include/database/PostgreSql.inc.php');
                $obj_database   = new Database_PostgreSql();
            break;
        }
        
        return $obj_database->checkLogin($a_data['sqlUsername'],$a_data['sqlPassword'],$a_data['sqlDatabase'],
                $a_data['sqlHost'],$a_data['sqlPort']);
    }
    
    /**
     * Checks if the FTP connection data is correct
     *
     * @param array $a_data	The connection data
     * @return boolean	True if the data is correct
     */
    public function checkFTP($a_data){
        require_once(NIV.'include/services/Service.inc.php');
        require_once(NIV.'include/services/FTP.inc.php');
        
        $obj_FTP   = new Service_FTP(false);
        $i_port = 21;
        if( $a_data['ftpPort'] != '' ) $i_port = $a_data['ftpPort'];
        return $obj_FTP->checkLogin($a_data['ftpUsername'],$a_data['ftpPassword'],$a_data['ftpHost'],$i_port = 21,$a_data['ftpType']);
    }
}

?>
