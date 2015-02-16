<?php
namespace core\models;

/**
 * Checks the access privileges from the current page
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 2.0
 *        @changed 06/02/2015
 *       
 *        Miniature-happiness is free software: you can redistribute it and/or modify
 *        it under the terms of the GNU Lesser General Public License as published by
 *        the Free Software Foundation, either version 3 of the License, or
 *        (at your option) any later version.
 *       
 *        Miniature-happiness is distributed in the hope that it will be useful,
 *        but WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *        GNU General Public License for more details.
 *       
 *        You should have received a copy of the GNU Lesser General Public License
 *        along with Scripthulp framework. If not, see <http://www.gnu.org/licenses/>.
 */
class Privileges
{

    protected $model_User;

    protected $service_Headers;

    protected $model_Config;

    protected $service_QueryBuilder;

    protected $service_Session;

    /**
     * PHP 5 constructor
     *
     * @param core\models\User $model_User
     *            The user model
     * @param core\services\Headers $service_Headers
     *            header service
     * @param core\services\QueryBuilder $service_QueryBuilder
     *            The query builder
     * @param core\services\Session $service_Session
     *            The session service
     * @param core\models\Config $model_Config
     *            The config model
     */
    public function __construct(\core\models\User $model_User, \core\services\Headers $service_Headers, \core\services\QueryBuilder $service_QueryBuilder, \core\services\Session $service_Session, \core\models\Config $model_Config)
    {
        $this->model_User = $model_User;
        $this->service_Headers = $service_Headers;
        $this->service_QueryBuilder = $service_QueryBuilder->createBuilder();
        $this->service_Session = $service_Session;
        $this->model_Config = $model_Config;
    }

    /**
     * Checks or the user is logged in and haves enough rights.
     */
    public function checkLogin()
    {
        if (stripos($this->model_Config->getPage(), '/phpunit') !== false) {
            /* Unit test */
            return;
        }
        
        $a_rights = $this->getPageRights();
        
        if (count($a_rights) == 0) {
            /* No rights defined */
            return;
        }
        
        $a_groups = $a_rights['groups'];
        $a_levels = $a_rights['levels'];
        
        /* Get redict url */
        $s_base = $this->model_Config->getBase();
        if ($s_base == '/') {
            $s_page = $_SERVER['REQUEST_URI'];
            if (substr($s_page, 0, 1) == '/') {
                $s_page = substr($s_page, 1);
            } else {
                $s_page = str_replace($s_base, '', $_SERVER['REQUEST_URI']);
            }
        }
        
        /* Get groups from user */
        $a_userGroups = $this->getUserGroups();
        
        $bo_found = false;
        foreach ($a_groups as $i_key => $i_group) {
            $i_level = $a_levels[$i_key];
            
            if (array_key_exists($i_group, $a_userGroups)) {
                if ($a_userGroups[$i_group] < $i_level) {
                    $this->status_403();
                } else {
                    $bo_found = true;
                    break;
                }
            }
        }
        
        if (! $bo_found) {
            if ($this->service_Session->exists('login')) {
                $this->status_403();
            } else {
                $this->status_401($s_page);
            }
        }
        
        if (! $this->checkCommand($a_userGroups)) {
            $this->status_403();
        }
        
        $i_userid = $this->service_Session->get('userid');
        define('USERID', $i_userid);
    }

    /**
     * Returns the set page rights
     *
     * @return array The page rights, an empty array if no rights are defined (open to all)
     */
    protected function getPageRights()
    {
        $this->service_QueryBuilder->select('group_pages', 'groupID,minLevel')
            ->getWhere()
            ->addAnd('page', 's', $this->model_Config->getPage());
        $service_Database = $this->service_QueryBuilder->getResult();
        
        if ($service_Database->num_rows() == 0) {
            return array();
        }
        
        $a_data = $service_Database->fetch_assoc();
        $a_rights = array(
            'groups' => array(),
            'levels' => array()
        );
        
        foreach ($a_data as $a_item) {
            $a_rights['levels'][] = (int) $a_item['minLevel'];
            $a_rights['groups'][] = (int) $a_item['groupID'];
        }
        
        return $a_rights;
    }

    /**
     * Returns the user groups and rights
     *
     * @return array The group rights, empty array if the user is not logged in
     */
    protected function getUserGroups()
    {
        if (! $this->service_Session->exists('login')) {
            return array();
        }
        
        if (! $this->checkFingerprint()) {
            return array();
        }
        
        $i_userid = $this->service_Session->get('userid');
        
        return $this->model_User->get($i_userid)->getGroups();
    }

    /**
     * Checks the fingerprint
     *
     * @return bool True if the fingerprint is valid
     */
    protected function checkFingerprint()
    {
        if (! $this->service_Session->exists('fingerprint') || ($this->service_Session->get('fingerprint') != $this->service_Session->getFingerprint())) {
            $this->service_Session->destroyLogin();
            
            return false;
        }
        
        return true;
    }

    /**
     * Checks the command privaliges
     *
     * @param array $a_userGroups
     *            The user group levels
     */
    protected function checkCommand($a_userGroups)
    {
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
        if ($service_Database->num_rows() == 0) {
            return true;
        }
        
        $a_rights = array(
            'groups' => array(),
            'levels' => array()
        );
        $a_data = $service_Database->fetch_assoc();
        
        foreach ($a_data as $a_item) {
            $a_rights['groups'][] = (int) $a_item['minLevel'];
            $a_rights['levels'][] = (int) $a_item['groupID'];
        }
        
        $bo_found = false;
        foreach ($a_rights['groups'] as $i_key => $i_group) {
            $i_level = $a_rights['levels'][$i_key];
            
            if (array_key_exists($i_group, $a_userGroups) && $a_userGroups[$i_group] >= $i_level) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Displays the login page
     *
     * @param string $s_page
     *            The current page
     */
    protected function status_401($s_page)
    {
        @ob_clean();
        
        $this->model_Config->setPage('authorization/login.php', 'index', 'default');
        
        $this->service_Session->set('page', $s_page);
        $this->service_Headers->http401();
        $this->service_Headers->printHeaders();
        require_once (NIV . 'authorization/login.php');
        
        $obj_login = new \Login();
        $obj_login->route('index');
        exit();
    }

    /**
     * Displays the 403 forbidden page
     */
    protected function status_403()
    {
        @ob_clean();
        
        $this->model_Config->setPage('errors/403.php', 'index', 'default');
        
        $this->service_Headers->http403();
        $this->service_Headers->printHeaders();
        require_once (NIV . 'errors/403.php');
        exit();
    }
}
