<?php
namespace admin\modules\general;

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
 * Admin user configuration class
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 */
class Users extends \core\AdminLogicClass
{

    /**
     *
     * @var \core\models\User
     */
    private $user;

    /**
     *
     * @var \core\models\Groups
     */
    private $groups;

    /**
     *
     * @var \core\models\Login
     */
    private $login;

    /**
     *
     * @var \core\services\Mailer
     */
    private $mailer;

    /**
     *
     * @var \core\helpers\AdminUserViews
     */
    private $view;

    /**
     * Starts the class Users
     *
     * @param \core\Input $input            
     * @param \core\models\Config $config            
     * @param \core\services\Language $language            
     * @param \core\services\Template $template            
     * @param \core\models\Groups $groups            
     * @param \core\models\User $user            
     * @param \core\services\Logs $logs                 
     * @param \core\models\Login $login            
     * @param \core\services\Mailer $mailer            
     * @param \core\helpers\AdminUserViews $view            
     */
    public function __construct(\core\Input $input, \core\models\Config $config, \core\services\Language $language, \core\services\Template $template, \core\models\Groups $groups, \core\models\User $user, \core\services\Logs $logs, \core\models\Login $login, \core\services\Mailer $mailer, \core\helpers\AdminUserViews $view)
    {
        parent::__construct($input, $config, $language, $template,$logs);
        
        $this->groups = $groups;
        $this->user = $user;
        $this->login = $login;
        $this->mailer = $mailer;
        $this->view = $view;
    }

    /**
     * Routes the controller
     *
     * @see Routable::route()
     */
    public function route($s_route)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            switch ($s_route) {
                case 'view':
                    $this->view();
                    break;
                
                case 'checkUsername':
                    if ($this->checkUsername($this->get['username'])) {
                        $this->template->set('result', '1');
                    } else {
                        $this->template->set('result', '0');
                    }
                    break;
                case 'checkEmail':
                    if ($this->checkEmail($this->get['email'])) {
                        $this->template->set('result', '1');
                    } else {
                        $this->template->set('result', '0');
                    }
                    break;
                case 'index':
                    $this->index();
                    break;
                
                case 'searchResults':
                    $this->search();
                    break;
                
                case 'addScreen':
                    $this->addScreen();
                    break;
                
                case 'editScreen':
                    $this->editScreen();
                    break;
                
                default:
                    throw new \BadFunctionCallException('Call to unkown GET call ' . $s_route . '.');
            }
        } else {
            switch ($s_route) {
                case 'add':
                    $this->add();
                    break;
                
                case 'edit':
                    $this->edit();
                    break;
                
                case 'delete':
                    $this->delete();
                    break;
                
                case 'addGroup':
                    $this->addGroup();
                    break;
                
                case 'deleteGroup':
                    $this->deleteGroup();
                    break;
                
                case 'login':
                    $this->login();
                    break;
                    
                default:
                    throw new \BadFunctionCallException('Call to unkown POST call ' . $s_route . '.');
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
    }

    /**
     * Adds the user access rights to the group
     */
    protected function addGroup()
    {
        try {
            $obj_User = $this->user->get($this->post['userid']);
        } catch (\Exception $e) {
            $this->logs->securityLog('Call to unknown user ' . $this->post['userid']);
            exit();
        }
        
        $this->groups->editUserLevel($obj_User->getID(), array(
            $this->post['group']
        ), $this->post['level']);
    }

    /**
     * Deletes the user access rights to the group
     */
    protected function deleteGroup()
    {
        try {
            $obj_User = $this->user->get($this->post['userid']);
        } catch (Exception $e) {
            $this->logs->securityLog('Call to unknown user ' . $this->post['userid']);
            exit();
        }
        
        $this->groups->editUserLevel($obj_User->getID(), array(
            $this->post['group']
        ), $this->post['level']);
    }

    /**
     * Logs the admin in as the given user
     */
    protected function login()
    {
        try {
            $obj_User = $this->user->get($this->post['userid']);
        } catch (Exception $e) {
            $this->logs->securityLog('Call to unknown user ' . $this->post['userid']);
            exit();
        }
        
        $this->login->loginAs($this->post['userid']);
    }

    /**
     * Generates the user overview
     */
    protected function index()
    {
        $a_users = $this->user->getUsers(0);
        $this->template->set('userid', USERID);
        
        $this->template->set('headerText', t('system/admin/users/users'));
        $this->template->set('textAdd', t('system/buttons/add'));
        $this->template->set('searchText', t('system/admin/users/searchText'));
        
        $this->template->set('header_ID', t('system/admin/users/id'));
        $this->template->set('header_username', t('system/admin/users/username'));
        $this->template->set('header_email', t('system/admin/users/email'));
        $this->template->set('header_loggedin', t('system/admin/users/loggedIn'));
        $this->template->set('header_registration', t('system/admin/users/registrated'));
        
        foreach ($a_users['data'] as $obj_user) {
            $a_data = array(
                'id' => $obj_user->getID(),
                'nick' => $obj_user->getUsername(),
                'email' => $obj_user->getEmail(),
                'registration' => ($obj_user->getRegistrated() != 0) ? date('d-m-Y H:i', $obj_user->getRegistrated()) : '-',
                'logged_in' => ($obj_user->lastLoggedIn() != 0) ? date('d-m-Y H:i', $obj_user->lastLoggedIn()) : '-'
            );
            
            $this->template->setBlock('users', $a_data);
        }
    }

    /**
     * Generates the search overview
     */
    protected function search()
    {
        $a_usersRaw = $this->user->searchUser($this->get['username']);
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
        
        $this->template->set('results', json_encode($a_users));
    }

    /**
     * Generates the user detail view
     */
    protected function view()
    {
        try {
            $obj_User = $this->user->get($this->get['userid']);
        } catch (\Exception $e) {
            $this->logs->securityLog('Call to unknown user ' . $this->get['userid']);
            exit();
        }
        
        $this->view->setModus('view');
        $this->view->setData($obj_User);
        $this->view->run();
    }

    /**
     * Generates the edit screen
     */
    protected function editScreen()
    {
        try {
            $obj_User = $this->user->get($this->get['userid']);
        } catch (\Exception $e) {
            $this->logs->securityLog('Call to unknown user ' . $this->get['userid']);
            exit();
        }
        
        $this->view->setModus('edit');
        $this->view->setData($obj_User);
        $this->view->run();
    }

    /**
     * Generates the add screen
     */
    protected function addScreen()
    {
        $this->view->run();
    }

    /**
     * Edits the given user
     */
    protected function edit()
    {
        try {
            $obj_User = $this->user->get($this->post['userid']);
        } catch (\Exception $e) {
            $this->logs->securityLog('Call to unknown user ' . $this->post['userid']);
            exit();
        }
        
        if (! isset($this->post['email']) || ! isset($this->post['bot']) || ($this->post['bot'] != 0 && $this->post['bot'] != 1) || ! isset($this->post['blocked']) || ($this->post['blocked'] != 0 && $this->post['blocked'] != 1))
            return;
            
            /* Check passwords */
        if ((! empty($this->post['password']))) {
            if ($this->post['password'] != $this->post['password2'])
                return;
            
            $obj_User->setPassword($this->post['password'], true);
            if ($obj_User->getEmail() != $this->post['email']) {
                $this->mailer->adminPasswordReset($obj_User->getUsername(), $obj_User->getEmail(), $this->post['password']);
            }
            $this->mailer->adminPasswordReset($obj_User->getUsername(), $this->post['email'], $this->post['password']);
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
    protected function add()
    {
        if (! isset($this->post['username']) || $this->post['username'] == '' || ! isset($this->post['email']) || ! isset($this->post['bot']) || ($this->post['bot'] != 0 && $this->post['bot'] != 1) || ! isset($this->post['password']) || $this->post['password'] == '' || ! isset($this->post['password2']) || $this->post['password2'] == '')
            return;
        
        if (! $this->service_Security->checkEmail($this->post['email']) || ! $this->user->checkUsername($this->post['username']) || ! $this->checkEmail($this->post['email']))
            return;
        
        if ($this->post['password'] != $this->post['password2'])
            return;
            
            /* Add user */
        $obj_User = $this->user->createUser();
        $obj_User->setUsername($this->post['username']);
        $obj_User->setEmail($this->post['email']);
        $obj_User->setPassword($this->post['password'], true);
        $obj_User->setBot($this->post['bot']);
        $obj_User->enableAccount();
        $obj_User->persist();
        
        /* Send notification email */
        $this->mailer->adminAdd($this->post['username'], $this->post['password'], $this->post['email']);
    }

    /**
     * Deletes the given user
     */
    protected function delete()
    {
        if ($this->post['userid'] == USERID)
            exit();
        
        try {
            $obj_User = $this->user->get($this->post['userid']);
        } catch (\Exception $e) {
            $this->logs->securityLog('Call to unknown user ' . $this->post['userid']);
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
    protected function checkUsername($s_username)
    {
        return $this->user->checkUsername($s_username);
    }

    /**
     * Checks if the email is available
     *
     * @return boolean if the email is available
     */
    protected function checkEmail($s_email)
    {
        return $this->user->checkEmail($s_email);
    }
}