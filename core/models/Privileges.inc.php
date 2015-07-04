<?php
namespace core\models;

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
 * Checks the access privileges from the current page
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 2.0
 */
class Privileges
{

    /**
     * 
     * @var \core\services\Headers
     */
    private $headers;

    /**
     * 
     * @var \core\models\Config
     */
    private $config;

    /**
     * 
     * @var \core\services\Builder
     */
    private $builder;

    /**
     * 
     * @var \core\services\Session
     */
    private $session;

    /**
     * 
     * @var \core\models\Groups
     */
    private $groups;

    /**
     * PHP 5 constructor
     *
     * @param core\services\Headers $headers
     * @param Builder $builder
     * @param core\models\Groups $groups
     * @param core\services\Session $session
     * @param core\models\Config $config
     */
    public function __construct(\core\services\Headers $headers, \Builder $builder, \core\models\Groups $groups, \core\services\Session $session, \core\models\Config $config)
    {
        $this->headers = $headers;
        $this->builder = $builder;
        $this->groups = $groups;
        $this->session = $session;
        $this->config = $config;
    }

    /**
     * Returns if the object schould be treated as singleton
     *
     * @return boolean True if the object is a singleton
     */
    public static function isSingleton()
    {
        return true;
    }

    /**
     * Checks or the user is logged in and haves enough rights.
     * Define the groep and level to overwrite the default rights for the page
     *
     * @param int $i_group
     *            id, optional
     * @param int $i_level
     *            level, optional
     * @param int $i_commandLevel
     *            The minimun level for the command, optional
     */
    public function checkLogin($i_group = -1, $i_level = -1, $i_commandLevel = -1)
    {
        \core\Memory::type('int', $i_group);
        \core\Memory::type('int', $i_level);
        \core\Memory::type('int', $i_commandLevel);
        
        if (stripos($this->config->getPage(), '/phpunit') !== false) {
            /* Unit test */
            return;
        }
        
        if ($i_group == - 1 || $i_level == - 1) {
            $this->builder->select('group_pages', 'groupID,minLevel')
                ->getWhere()
                ->addAnd('page', 's', $this->config->getPage());
            $service_Database = $this->builder->getResult();
            
            $i_group = 1;
            $i_level = \core\services\Session::ANONYMOUS;
            
            if ($service_Database->num_rows() > 0) {
                $i_level = (int) $service_Database->result(0, 'minLevel');
                $i_group = (int) $service_Database->result(0, 'groupID');
            }
        }
        
        $this->checkSSL($i_level);
        
        if ($i_level == \core\services\Session::ANONYMOUS) {
            if (($this->session->exists('login')) && ($this->session->exists('userid'))) {
                if (! defined('USERID')) {
                    define('USERID', $this->session->get('userid'));
                }
            }
            
            return;
        }
        
        /* Get redict url */
        $s_base = $this->config->getBase();
        $s_page = $_SERVER['REQUEST_URI'];
        if ($s_base != '/') {
            $s_page = str_replace($s_base, '', $s_page);
        }
        
        if( !$this->checkloginStatus($s_page) ){
            return;
        }
        
        if( !$this->checkFingerprint($s_page) ){
            return;
        }
        
        /* Check fingerprint */
        $i_userid = $this->session->get('userid');
        $i_userLevel = $this->groups->getLevelByGroupID($i_group, $i_userid);
        
        if (($i_userLevel < $i_level)) {
            /*
             * Insuffient rights or no access too the group No access
             */
            $_GET['command'] = 'index';
            $_SERVER['SCRIPT_NAME'] = 'errors/Error403.php';
            $this->config->setAjax(false);
            return;
        }
        
        $this->checkCommand($i_commandLevel, $i_userid);
        
        if (! defined('USERID')) {
            define('USERID', $i_userid);
        }
    }

    /**
     * Checks the login status
     *
     * @param String $s_page
     *            The current page
     */
    private function checkloginStatus($s_page)
    {
        if ($this->session->exists('login')) {
            return true;
        }
        
        if ($this->config->isAjax()) {
            $this->headers->http401();
            $this->headers->printHeaders();
            die();
        }
        
        $this->session->set('page', $s_page);
        $this->config->setPage('authorization/login', 'index');
        $this->headers->http401();
        $this->headers->printHeaders();
        
        $_GET['command'] = 'index';
        $_SERVER['SCRIPT_NAME'] = 'authorization/login.php';
       
        return false;
    }

    /**
     * Checks the fingerprint
     *
     * @param String $s_page
     *            The current page
     */
    private function checkFingerprint($s_page)
    {
        if (! $this->session->exists('fingerprint') || ($this->session->get('fingerprint') != $this->session->getFingerprint())) {
            $this->session->destroyLogin();
            
            $this->session->set('page', $s_page);
            $this->headers->http401();
            $this->headers->printHeaders();
            
            $_GET['command'] = 'index';
            $_SERVER['SCRIPT_NAME'] = 'authorization/login.php';
            $this->config->setAjax(false);
            
            return false;
        }
        
        return true;
    }

    /**
     * Checks the command privaliges
     *
     * @param int $i_commandLevel
     *            The minimun command access level, -1 for auto detect
     * @param int $i_userid
     *            The userid
     */
    private function checkCommand($i_commandLevel, $i_userid)
    {
        if ($i_commandLevel != - 1) {
            $this->builder->select('group_pages_command', 'groupID,minLevel')
                ->getWhere()
                ->addAnd(array(
                'page',
                'command'
            ), array(
                's',
                's'
            ), array(
                $this->config->getPage(),
                $this->config->getCommand()
            ));
            $service_Database = $this->builder->getResult();
            if ($service_Database->num_rows() > 0) {
                $i_commandLevel = (int) $service_Database->result(0, 'minLevel');
                $i_group = (int) $service_Database->result(0, 'groupID');
                
                $i_level = $this->groups->getLevelByGroupID($i_group, $i_userid);
            }
        }
        
        if (($i_commandLevel != - 1) && ($i_level < $i_commandLevel)) {
            /*
             * Insuffient rights No access
             */
            $_GET['command'] = 'index';
            $_SERVER['SCRIPT_NAME'] = 'errors/Error403.php';
            $this->config->setAjax(false);
        }
    }
    
    /**
     * Checks the ssl setting
     * 
     * @param int $i_level  The minimun page level
     */
    private function checkSSL($i_level){
        $i_ssl = $this->config->isSslEnabled();
        if( defined('FORCE_SSL') ){
            $i_ssl = \core\services\Settings::SSL_ALL;
        }
    
        if( $this->config->isSLL() || ($i_ssl == \core\services\Settings::SSL_DISABLED) ){
            return;
        }        
        
        if( ($i_level == \core\services\Session::ANONYMOUS) && ($i_ssl == \core\services\Settings::SSL_LOGIN) && (stripos($_SERVER['REQUEST_URI'],'authorization')) === false){
            return;
        }
        
        $this->headers->redirect('https://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['REQUEST_URI']);
        $this->headers->printHeaders();
        exit();
    }
}