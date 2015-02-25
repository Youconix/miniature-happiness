<?php
namespace admin;

/**
 * Admin user configuration class
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
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
 *        along with Miniature-happiness. If not, see <http://www.gnu.org/licenses/>.
 */
define('NIV', '../../../');
include (NIV . 'core/AdminLogicClass.php');

class Users extends \core\AdminLogicClass
{

    private $model_Groups;

    /**
     * Starts the class Users
     */
    public function __construct()
    {
        $this->init();
        
        if (! \core\Memory::models('Config')->isAjax()) {
            exit();
        }
        
        if (isset($this->get['command'])) {
            if ($this->get['command'] == 'view') {
                $this->view();
            } else 
                if ($this->get['command'] == 'checkUsername') {
                    if ($this->checkUsername($this->get['username'])) {
                        $this->service_Template->set('result', '1');
                    } else {
                        $this->service_Template->set('result', '0');
                    }
                } else 
                    if ($this->get['command'] == 'checkEmail') {
                        if ($this->checkEmail($this->get['email'])) {
                            $this->service_Template->set('result', '1');
                        } else {
                            $this->service_Template->set('result', '0');
                        }
                    } else 
                        if ($this->get['command'] == 'index') {
                            $this->index();
                        } else 
                            if ($this->get['command'] == 'searchResults') {
                                $this->search();
                            } else 
                                if ($this->get['command'] == 'addScreen') {
                                    $this->addScreen();
                                } else 
                                    if ($this->get['command'] == 'editScreen') {
                                        $this->editScreen();
                                    }
        } else 
            if (isset($this->post['command'])) {
                if ($this->post['command'] == 'add') {
                    $this->add();
                } else 
                    if ($this->post['command'] == 'edit') {
                        $this->edit();
                    } else 
                        if ($this->post['command'] == 'delete') {
                            $this->delete();
                        } else 
                            if ($this->post['command'] == 'addGroup') {
                                $this->addGroup();
                            } else 
                                if ($this->post['command'] == 'deleteGroup') {
                                    $this->deleteGroup();
                                } else 
                                    if ($this->post['command'] == 'login') {
                                        $this->login();
                                    }
            }
    }

    /**
     * Inits the class Users
     */
    protected function init()
    {
        $this->init_get = array(
            'userid' => 'int',
            'command' => 'string',
            'email' => 'string-DB',
            'username' => 'string-DB',
            'page' => 'int'
        );
        
        $this->init_post = array(
            'command' => 'string',
            'userid' => 'int',
            'username' => 'string-DB',
            'email' => 'string-DB',
            'password' => 'string-DB',
            'password2' => 'string-DB',
            'bot' => 'int',
            'blocked' => 'int',
            'groupID' => 'int',
            'level' => 'int'
        );
        
        parent::init();
        
        $this->model_Groups = \core\Memory::models('Groups');
    }

    /**
     * Adds the user access rights to the group
     */
    private function addGroup()
    {
        try {
            $obj_User = $this->model_User->get($this->post['userid']);
        } catch (Exception $e) {
            \core\Memory::services('Logs')->securityLog('Call to unknown user ' . $this->post['userid']);
            $this->service_Session->destroyLogin();
            exit();
        }
        
        $model_Group = \core\Memory::models('Group');
        $model_Group->editUserLevel($obj_User->getID(), array(
            $this->post['group']
        ), $this->post['level']);
    }

    /**
     * Deletes the user access rights to the group
     */
    private function deleteGroup()
    {
        try {
            $obj_User = $this->model_User->get($this->post['userid']);
        } catch (Exception $e) {
            \core\Memory::services('Logs')->securityLog('Call to unknown user ' . $this->post['userid']);
            $this->service_Session->destroyLogin();
            exit();
        }
        
        $model_Group = \core\Memory::models('Group');
        $model_Group->editUserLevel($obj_User->getID(), array(
            $this->post['group']
        ), $this->post['level']);
    }

    /**
     * Logs the admin in as the given user
     */
    protected function login()
    {
        try {
            $obj_User = $this->model_User->get($this->post['userid']);
        } catch (Exception $e) {
            \core\Memory::services('Logs')->securityLog('Call to unknown user ' . $this->post['userid']);
            $this->service_Session->destroyLogin();
            exit();
        }
        
        \core\Memory::models('Login')->loginAs($this->post['userid']);
    }

    /**
     * Generates the user overview
     */
    private function index()
    {
        $i_page = 1;
        if (isset($this->get['page']) && $this->get['page'] > 0) {
            $i_page = $this->get['page'];
        }
        
        $i_start = $i_page * 25 - 25;
        
        $a_users = $this->model_User->getUsers($i_start);
        $this->service_Template->set('userid', USERID);
        
        $this->service_Template->set('headerText', t('system/admin/users/users'));
        $this->service_Template->set('textAdd', t('system/buttons/add'));
        $this->service_Template->set('searchText', t('system/admin/users/searchText'));
        
        $this->service_Template->set('header_ID', t('system/admin/users/id'));
        $this->service_Template->set('header_username', t('system/admin/users/username'));
        $this->service_Template->set('header_email', t('system/admin/users/email'));
        $this->service_Template->set('header_loggedin', t('system/admin/users/loggedIn'));
        $this->service_Template->set('header_registration', t('system/admin/users/registrated'));
        
        foreach ($a_users['data'] as $obj_user) {
            $a_data = array(
                'id' => $obj_user->getID(),
                'nick' => $obj_user->getUsername(),
                'email' => $obj_user->getEmail(),
                'registration' => ($obj_user->getRegistrated() != 0) ? date('d-m-Y H:i', $obj_user->getRegistrated()) : '-',
                'logged_in' => ($obj_user->lastLoggedIn() != 0) ? date('d-m-Y H:i', $obj_user->lastLoggedIn()) : '-'
            );
            
            $this->service_Template->setBlock('users', $a_data);
        }
        
        $helper_Nav = \core\Memory::helpers('PageNavigation');
        $helper_Nav->setAmount($a_users['number'])
            ->setPage($i_page)
            ->setUrl('javascript:adminUsers.view({page})');
        $this->service_Template->set('nav', $helper_Nav->generateCode());
    }

    /**
     * Generates the search overview
     */
    private function search()
    {
        $a_usersRaw = $this->model_User->searchUser($this->get['username']);
        $a_users = array();
        
        foreach ($a_usersRaw['data'] as $obj_user) {
            $item = array(
                'id' => $obj_user->getID(),
                'username' => $obj_user->getUsername(),
                'email' => $obj_user->getEmail(),
                'loggedin' => $obj_user->lastLoggedIn(),
                'registrated' => $obj_user->getRegistrated()
            );
            
            ($item['loggedin'] == 0) ? $item['loggedin'] = '-' : $item['loggedin'] = date('d-m-Y H:i', $item['loggedin']);
            ($item['registrated'] == 0) ? $item['registrated'] = '-' : $item['registrated'] = date('d-m-Y H:i', $item['registrated']);
            
            $a_users[] = $item;
        }
        
        $this->service_Template->set('results', json_encode($a_users));
    }

    /**
     * Generates the user detail view
     */
    private function view()
    {
        try {
            $obj_User = $this->model_User->get($this->get['userid']);
        } catch (Exception $e) {
            \core\Memory::services('Logs')->securityLog('Call to unknown user ' . $this->get['userid']);
            $this->service_Session->destroyLogin();
            exit();
        }
        
        $helper_view = \core\Memory::helpers('AdminUserView');
        $helper_view->setData($obj_User);
        $helper_view->run();
    }

    /**
     * Generates the edit screen
     */
    private function editScreen()
    {
        try {
            $obj_User = $this->model_User->get($this->get['userid']);
        } catch (Exception $e) {
            \core\Memory::services('Logs')->securityLog('Call to unknown user ' . $this->get['userid']);
            $this->service_Session->destroyLogin();
            exit();
        }
        
        $helper_view = \core\Memory::helpers('AdminUserEdit');
        $helper_view->setData($obj_User);
        $helper_view->run();
    }

    /**
     * Generates the add screen
     */
    private function addScreen()
    {
        $helper_view = \core\Memory::helpers('AdminUserAdd');
        $helper_view->run();
    }

    /**
     * Edits the given user
     */
    private function edit()
    {
        try {
            $obj_User = $this->model_User->get($this->post['userid']);
        } catch (Exception $e) {
            \core\Memory::services('Logs')->securityLog('Call to unknown user ' . $this->post['userid']);
            $this->service_Session->destroyLogin();
            exit();
        }
        
        if (! isset($this->post['email']) || ! isset($this->post['bot']) || ($this->post['bot'] != 0 && $this->post['bot'] != 1) || ! isset($this->post['blocked']) || ($this->post['blocked'] != 0 && $this->post['blocked'] != 1))
            return;
            
            /* Check passwords */
        if ((! empty($this->post['password']))) {
            if ($this->post['password'] != $this->post['password2'])
                return;
            
            $obj_User->setPassword($this->post['password'], true);
            $obj_mailer = \core\Memory::services('Mailer');
            if ($obj_User->getEmail() != $this->post['email']) {
                $obj_mailer->adminPasswordReset($obj_User->getUsername(), $obj_User->getEmail(), $this->post['password']);
            }
            $obj_mailer->adminPasswordReset($obj_User->getUsername(), $this->post['email'], $this->post['password']);
        }
        
        /* Edit user */
        $obj_User->setEmail($this->post['email']);
        $obj_User->setBot($this->post['bot']);
        $obj_User->setBlocked($this->post['blocked']);
        $obj_User->persist();
    }

    /**
     * Adds a new user to the database
     */
    private function add()
    {
        if (! isset($this->post['username']) || $this->post['username'] == '' || ! isset($this->post['email']) || ! isset($this->post['bot']) || ($this->post['bot'] != 0 && $this->post['bot'] != 1) || ! isset($this->post['password']) || $this->post['password'] == '' || ! isset($this->post['password2']) || $this->post['password2'] == '')
            return;
        
        if (! $this->service_Security->checkEmail($this->post['email']) || ! $this->model_User->checkUsername($this->post['username']) || ! $this->checkEmail($this->post['email']))
            return;
        
        if ($this->post['password'] != $this->post['password2'])
            return;
            
            /* Add user */
        $obj_User = $this->model_User->createUser();
        $obj_User->setUsername($this->post['username']);
        $obj_User->setEmail($this->post['email']);
        $obj_User->setPassword($this->post['password'], true);
        $obj_User->setBot($this->post['bot']);
        $obj_User->enableAccount();
        $obj_User->persist();
        
        /* Send notification email */
        \core\Memory::services('Mailer')->adminAdd($this->post['username'], $this->post['password'], $this->post['email']);
    }

    /**
     * Deletes the given user
     */
    private function delete()
    {
        if ($this->post['userid'] == USERID)
            exit();
        
        try {
            $obj_User = $this->model_User->get($this->post['userid']);
        } catch (Exception $e) {
            \core\Memory::services('Logs')->securityLog('Call to unknown user ' . $this->post['userid']);
            $this->service_Session->destroyLogin();
            exit();
        }
        
        /* Say bye bye */
        $obj_User->delete();
    }

    /**
     * Checks if the username is available
     *
     * @return boolean if the username is available
     */
    private function checkUsername($s_username)
    {
        return $this->model_User->checkUsername($s_username);
    }

    /**
     * Checks if the email is available
     *
     * @return boolean if the email is available
     */
    private function checkEmail($s_email)
    {
        return $this->model_User->checkEmail($s_email);
    }
}

$obj_Users = new Users();
unset($obj_Users);