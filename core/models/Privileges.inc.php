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

    private $service_Headers;

    private $model_Config;

    private $service_QueryBuilder;

    private $service_Session;

    private $model_Groups;

    /**
     * PHP 5 constructor
     *
     * @param core\services\Headers $service_Headers
     *            header service
     * @param core\services\QueryBuilder $service_QueryBuilder
     *            The query builder
     * @param core\models\Groups $model_Groups
     *            The groups model
     * @param core\services\Session $service_Session
     *            The session service
     * @param core\models\Config $model_Config
     *            The config model
     */
    public function __construct(\core\services\Headers $service_Headers, \core\services\QueryBuilder $service_QueryBuilder, \core\models\Groups $model_Groups, \core\services\Session $service_Session, \core\models\Config $model_Config)
    {
        $this->service_Headers = $service_Headers;
        $this->service_QueryBuilder = $service_QueryBuilder->createBuilder();
        $this->model_Groups = $model_Groups;
        $this->service_Session = $service_Session;
        $this->model_Config = $model_Config;
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
     * @throws MemoryException the page rights are not defined with arguments or database
     */
    public function checkLogin($i_group = -1, $i_level = -1, $i_commandLevel = -1)
    {
        \core\Memory::type('int', $i_group);
        \core\Memory::type('int', $i_level);
        \core\Memory::type('int', $i_commandLevel);
        
        if (stripos($this->model_Config->getPage(), '/phpunit') !== false) {
            /* Unit test */
            return;
        }
        
        if ($i_group == - 1 || $i_level == - 1) {
            $this->service_QueryBuilder->select('group_pages', 'groupID,minLevel')
                ->getWhere()
                ->addAnd('page', 's', $this->model_Config->getPage());
            $service_Database = $this->service_QueryBuilder->getResult();
            
            $i_group = 1;
            $i_level = \core\services\Session::ANONYMOUS;
            
            if ($service_Database->num_rows() > 0) {
                $i_level = (int) $service_Database->result(0, 'minLevel');
                $i_group = (int) $service_Database->result(0, 'groupID');
            }
        }
        
        if ($i_level == \core\services\Session::ANONYMOUS) {
            if (($this->service_Session->exists('login')) && ($this->service_Session->exists('userid'))) {
                if (! defined('USERID')) {
                    define('USERID', $this->service_Session->get('userid'));
                }
            }
            
            return;
        }
        
        /* Get redict url */
        $s_base = $this->model_Config->getBase();
        $s_page = $_SERVER['REQUEST_URI'];
        if ($s_base != '/') {
            $s_page = str_replace($s_base, '', $s_page);
        }
        
        $this->checkloginStatus($s_page);
        
        $this->checkFingerprint($s_page);
        
        /* Check fingerprint */
        $i_userid = $this->service_Session->get('userid');
        $i_userLevel = $this->model_Groups->getLevelByGroupID($i_group, $i_userid);
        
        if (($i_userLevel < $i_level)) {
            /*
             * Insuffient rights or no access too the group No access
             */
            $this->service_Headers->http403();
            $this->service_Headers->printHeaders();
            include (NIV . 'errors/403.php');
            exit();
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
        if ($this->service_Session->exists('login')) {
            return;
        }
        
        $this->service_Headers->http401();
        $this->service_Headers->printHeaders();
        
        if ($this->model_Config->isAjax()) {
            die();
        }
        
        $this->service_Session->set('page', $s_page);
        $this->model_Config->setPage('authorization/login', 'index');
        
        require (NIV . 'authorization/login.php');
        $obj_login = new \Login();
        $obj_login->route('index');
        exit();
    }

    /**
     * Checks the fingerprint
     *
     * @param String $s_page
     *            The current page
     */
    private function checkFingerprint($s_page)
    {
        if (! $this->service_Session->exists('fingerprint') || ($this->service_Session->get('fingerprint') != $this->service_Session->getFingerprint())) {
            // $this->service_Session->destroyLogin();
            
            echo ('fingerprint fail');
            echo ($this->service_Session->get('fingerprint') . '  ' . $this->service_Session->getFingerprint());
            die();
            $this->service_Session->set('page', $s_page);
            $this->service_Headers->http401();
            $this->service_Headers->redirect('authorization/login/index');
        }
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
            $this->service_QueryBuilder->select('group_pages_command', 'groupID,minLevel')
                ->getWhere()
                ->addAnd(array(
                'page',
                'command'
            ), array(
                's',
                's'
            ), array(
                $this->model_Config->getPage(),
                $this->model_Config->getCommand()
            ));
            $service_Database = $this->service_QueryBuilder->getResult();
            if ($service_Database->num_rows() > 0) {
                $i_commandLevel = (int) $service_Database->result(0, 'minLevel');
                $i_group = (int) $service_Database->result(0, 'groupID');
                
                $i_level = $this->model_Groups->getLevelByGroupID($i_group, $i_userid);
            }
        }
        
        if (($i_commandLevel != - 1) && ($i_level < $i_commandLevel)) {
            /*
             * Insuffient rights No access
             */
            $this->service_Headers->http403();
            $this->service_Headers->printHeaders();
            require (NIV . 'errors/403.php');
            exit();
        }
    }
}