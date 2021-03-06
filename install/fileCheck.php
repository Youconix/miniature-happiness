<?php

/**
 * File check class
 * This file checks if all the critical classes are present before install
 *
 * This file is part of the Scripthulp framework installer
 *
 * @copyright 2012,2013,2014  Rachelle Scheijen
 * @author    Rachelle Scheijen
 * @since     1.0
 * @changed   31/08/2014
 *
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
class FileCheck {

    private $bo_valid = true;
    private $s_valid = '<span class="Notice">V</span>';
    private $s_inValid = '<span class="errorNotice">X</span>';

    /**
     * Validates the framework files
     *
     * @return String	The validation result
     */
    public function validate() {
        $s_output = '' .
                $this->admin() .
                $this->error_docs() .
                $this->database() .
                $this->exceptions() .
                $this->helpers() .
                $this->language() .
                $this->mailer() .
                $this->domPDF() .
                $this->models() .
                $this->services() .
                $this->includeDir();

        return $s_output;
    }

    /**
     * Returns the valid status
     * 
     * @return boolean	True if the framework is valid
     */
    public function isValid() {
        return $this->bo_valid;
    }

    /**
     * Checks the admin panel
     *
     * @return String	The validation result
     */
    private function admin() {
        $i_invalid = 0;
        $i_total = 0;
        $s_output = '';

        $a_files = array('groups', 'index', 'logs', 'maintenance', 'settings', 'SettingsMain', 'stats', 'users');
        $a_filesUser = array();

        $a_files = array_merge($a_files, $a_filesUser);
        foreach ($a_files AS $s_file) {
            $i_total++;
            if (!file_exists(NIV . 'admin/' . $s_file . '.php')) {
                $s_output .= '<li class="errorNotice">admin/' . $s_file . '.php</li>';
                $this->bo_valid = false;
                $i_invalid++;
            }
        }

        return '<h2>Admin panel '.$this->getCounter($i_invalid,$i_total).'</h2>'
                . '<ul>' .$s_output . '</ul>';
    }

    /**
     * Checks the error documents
     * 
     * @return String	The validation result
     */
    private function error_docs() {
        $i_invalid = 0;
        $i_total = 0;
        $s_output = '';

        $a_files = array('403', '404', '500');
        foreach ($a_files AS $s_file) {
            $i_total++;
            if (!file_exists(NIV . 'errors/' . $s_file . '.php')) {
                $s_output .= '<li class="errorNotice">errors/' . $s_file . '.php</li>';
                $this->bo_valid = false;
                $i_invalid++;
            }
        }

        return '<h2>Error files '.$this->getCounter($i_invalid,$i_total).'</h2>
		
		<ul>' . $s_output . '</ul>';
    }

    /**
     * Checks the database access layers
     *
     * @return String	The validation result
     */
    private function database(){
        $i_invalid = 0;
        $i_total = 0;
        $s_output = '';

        $a_files = array('builder_Mysqli', 'Mysqli', 'mysqli_binded', 'PostgreSql');
        foreach ($a_files AS $s_file) {
            $i_total++;
            if (!file_exists(NIV . 'include/database/' . $s_file . '.inc.php')) {
                $s_output .= '<li class="errorNotice">include/database/' . $s_file . '.inc.php</li>';
                $this->bo_valid = false;
                $i_invalid++;
            }
        }

        return '<h2>Database DALs '.$this->getCounter($i_invalid,$i_total).'</h2>
		
		<ul>'.$s_output . '</ul>';
    }

    /**
     * Checks the exceptions
     * 
     * @return String	The validation result
     */
    private function exceptions() {
        $i_invalid = 0;
        $i_total = 0;
        $s_output = '';

        $a_files = array('DateException', 'DBException', 'GeneralException', 'IllegalArgumentException', 'IOException', 'MemoryException', 'NullPointerException', 'TemplateException', 'TypeException', 'XMLException');
        foreach ($a_files AS $s_file) {
            $i_total++;
            if (!file_exists(NIV . 'include/exceptions/' . $s_file . '.inc.php')) {
                $s_output .= '<li class="errorNotice">include/exceptions/' . $s_file . '.inc.php</li>';
                $this->bo_valid = false;
                $i_invalid++;
            }
        }

        return '<h2>Exceptions '.$this->getCounter($i_invalid,$i_total).'</h2>
		
		<ul>'.$s_output . '</ul>';
    }

    /**
     * Checks the helpers
     * 
     * @return String	The validation result
     */
    private function helpers() {
        $i_invalid = 0;
        $i_total = 0;
        $s_output = '';

        $a_files = array('Calender', 'Captcha', 'CheckList', 'Date', 'Helper', 'RadioList', 'UBB', 'UniqueCheckList', 'UniqueRadioList');
        $a_filesUser = array();

        $a_files = array_merge($a_files, $a_filesUser);
        foreach ($a_files AS $s_file) {
            $i_total++;
            if (!file_exists(NIV . 'include/helpers/' . $s_file . '.inc.php')) {
                $s_output .= '<li class="errorNotice">include/helpers/' . $s_file . '.inc.php</li>';
                $this->bo_valid = false;
                $i_invalid++;
            }
        }

        return '<h2>Helpers '.$this->getCounter($i_invalid,$i_total).'</h2>
		
		<ul>'.$s_output . '</ul>';
    }

    /**
     * Checks the language files
     * 
     * @return String	The validation result
     */
    private function language() {
        $i_invalid = 0;
        $i_total = 0;
        $s_output = '';

        $a_languages = array('nl_NL', 'en_UK');
        $a_files = array('site', 'system');
        foreach($a_languages AS $s_language){
            foreach ($a_files AS $s_file) {
                $i_total++;
                if (!file_exists(NIV . 'include/language/'.$s_language.'/' . $s_file . '.lang')) {
                    $s_output .= '<li class="errorNotice">include/language/'.$s_language.'/' . $s_file . '.lang</li>';
                    $this->bo_valid = false;
                    $i_invalid++;
                }
            }
        }

        return '<h2>Language files '.$this->getCounter($i_invalid,$i_total).'</h2>
		
		<ul>'.$s_output . '</ul>';
    }

    /**
     * Checks the mailer class files
     * 
     * @return String	The validation result
     */
    private function mailer() {
        $i_invalid = 0;
        $i_total = 0;
        $s_output = '';

        $a_files = array('phpmailer', 'pop3', 'smtp');
        foreach ($a_files AS $s_file) {
            $i_total++;
            if (!file_exists(NIV . 'include/mailer/class.' . $s_file . '.php')) {
                $s_output .= '<li class="errorNotice">include/mailer/class.' . $s_file . '.php</li>';
                $this->bo_valid = false;
                $i_invalid++;
            }
        }

        return '<h2>PHP Mailer '.$this->getCounter($i_invalid,$i_total).'</h2>
		
		<ul>'.$s_output . '</ul>';
    }

    /**
     * Checks the domPDF files
     * 
     * @return String	The validation result
     */
    private function domPDF() {
        $i_invalid = 0;
        $i_total = 0;
        $s_output = '';

        $a_files = array('dompdf_config.custom.inc', 'dompdf_config.inc', 'dompdf', 'index', 'load_font');
        foreach ($a_files AS $s_file) {
            $i_total++;
            if (!file_exists(NIV . 'include/dompdf/' . $s_file . '.php')) {
                $s_output .= '<li class="errorNotice">include/dompdf/' . $s_file . '.php</li>';
                $this->bo_valid = false;
                $i_invalid++;
            }
        }

        return '<h2>DomPDF '.$this->getCounter($i_invalid,$i_total).'</h2>
		
		<ul>'.$s_output . '</ul>';
    }

    /**
     * Checks the models
     * 
     * @return String	The validation result
     */
    private function models() {
        $i_invalid = 0;
        $i_total = 0;
        $s_output = '';

        $a_files = array('GeneralUser', 'Groups', 'Model', 'PM', 'Stats', 'User');
        $a_filesUser = array();

        $a_files = array_merge($a_files, $a_filesUser);
        foreach ($a_files AS $s_file) {
            $i_total++;
            if (!file_exists(NIV . 'include/models/' . $s_file . '.inc.php')) {
                $s_output .= '<li class="errorNotice">include/models/' . $s_file . '.inc.php</li>';
                $this->bo_valid = false;
                $i_invalid++;
            }
        }

        $a_files = array('Data_Group', 'Data_PM', 'Data_User');
        $a_filesUser = array();

        $a_files = array_merge($a_files, $a_filesUser);
        foreach ($a_files AS $s_file) {
            $i_total++;
            if (!file_exists(NIV . 'include/models/data/' . $s_file . '.inc.php')) {
                $s_output .= '<li class="errorNotice">include/models/data/' . $s_file . '.inc.php</li>';
                $this->bo_valid = false;
                $i_invalid++;
            }
        }

        return '<h2>Models '.$this->getCounter($i_invalid,$i_total).'</h2>
		
		<ul>'.$s_output . '</ul>';
    }

    /**
     * Checks the services
     * 
     * @return String	The validation result
     */
    private function services() {
        $i_invalid = 0;
        $i_total = 0;
        $s_output = '';

        $a_files = array('Authorization', 'Cookie', 'CurlManager', 'Database', 'ErrorHandler', 'AuthorizationFacebook', 'File', 'FileData', 'FTP', 'Language', 'Logs', 'Mailer', 'Maintenance', 'Random', 'Security', 'Service', 'Session', 'Template', 'QueryBuilder', 'Upload', 'Xml', 'Settings');
        $a_filesUser = array();

        $a_files = array_merge($a_files, $a_filesUser);
        foreach ($a_files AS $s_file) {
            $i_total++;
            if (!file_exists(NIV . 'include/services/' . $s_file . '.inc.php')) {
                $s_output .= '<li class="errorNotice">include/services/' . $s_file . '.inc.php</li>';
                $this->bo_valid = false;
                $i_invalid++;
            }
        }

        return '<h2>Services '.$this->getCounter($i_invalid,$i_total).'</h2>
		
		<ul>'.$s_output . '</ul>';
    }

    /**
     * Checks the include directory
     * 
     * @return String	The validation result
     */
    private function includeDir() {
        $i_invalid = 0;
        $i_total = 0;
        $s_output = '';

        $a_files = array('AdminLogicClass', 'BaseClass', 'BaseLogicClass', 'Footer', 'Header', 'Memory', 'Menu', 'MenuAdmin', 'PdfLogicClass');
        $a_filesUser = array();

        $a_files = array_merge($a_files, $a_filesUser);
        foreach ($a_files AS $s_file) {
            $i_total++;
            if (!file_exists(NIV . 'include/' . $s_file . '.php')) {
                $s_output .= '<li class="errorNotice">include/' . $s_file . '.php</li>';
                $this->bo_valid = false;
                $i_invalid++;
            }
        }

        return '<h2>Include directory '.$this->getCounter($i_invalid,$i_total).'</h2>
		
		<ul>'.$s_output . '</ul>';
    }
    
    private function getCounter($i_invalid,$i_total){
        ($i_invalid == 0 )? $s_className = 'Notice' : $s_className = 'errorNotice';
        
        return '<span class="'.$s_className.'">'.($i_total-$i_invalid).'/'.$i_total.'</span>';
    }
}
