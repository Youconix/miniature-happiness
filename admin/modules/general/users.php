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
    private $model_User;

    /**
     *
     * @var \core\services\Logs
     */
    private $service_Logs;

    /**
     *
     * @var \core\models\Groups
     */
    private $model_Groups;

    /**
     *
     * @var \core\services\Session
     */
    private $service_Session;

    /**
     *
     * @var \core\models\Login
     */
    private $model_Login;

    /**
     *
     * @var \core\services\Mailer
     */
    private $service_Mailer;

    /**
     *
     * @var \core\helpers\AdminUserViews
     */
    private $helper_view;

    /**
     * Starts the class Users
     *
     * @param \core\services\Security $service_Security            
     * @param \core\models\Config $model_Config            
     * @param \core\services\Language $service_Language            
     * @param \core\services\Template $service_Template            
     * @param \core\models\Groups $model_Groups            
     * @param \core\models\User $model_User            
     * @param \core\services\Logs $service_Logs            
     * @param \core\services\Session $service_Session            
     * @param \core\models\Login $model_Login            
     * @param \core\services\Mailer $service_Mailer            
     * @param \core\helpers\AdminUserViews $helper_view            
     */
    public function __construct(\core\services\Security $service_Security, \core\models\Config $model_Config, \core\services\Language $service_Language, \core\services\Template $service_Template, \core\models\Groups $model_Groups, \core\models\User $model_User, \core\services\Logs $service_Logs, \core\services\Session $service_Session, \core\models\Login $model_Login, \core\services\Mailer $service_Mailer, \core\helpers\AdminUserViews $helper_view)
    {
        parent::__construct($service_Security, $model_Config, $service_Language, $service_Template);
        
        $this->model_Groups = $model_Groups;
        $this->model_User = $model_User;
        $this->service_Logs = $service_Logs;
        $this->service_Session = $service_Session;
        $this->model_Login = $model_Login;
        $this->service_Mailer = $service_Mailer;
        $this->helper_view = $helper_view;
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
                        $this->service_Template->set('result', '1');
                    } else {
                        $this->service_Template->set('result', '0');
                    }
                    break;
                case 'checkEmail':
                    if ($this->checkEmail($this->get['email'])) {
                        $this->service_Template->set('result', '1');
                    } else {
                        $this->service_Template->set('result', '0');
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
            $obj_User = $this->model_User->get($this->post['userid']);
        } catch (\Exception $e) {
            $this->service_Logs->securityLog('Call to unknown user ' . $this->post['userid']);
            $this->service_Session->destroyLogin();
            exit();
        }
        
        $this->model_Groups->editUserLevel($obj_User->getID(), array(
            $this->post['group']
        ), $this->post['level']);
    }

    /**
     * Deletes the user access rights to the group
     */
    protected function deleteGroup()
    {
        try {
            $obj_User = $this->model_User->get($this->post['userid']);
        } catch (Exception $e) {
            $this->service_Session->securityLog('Call to unknown user ' . $this->post['userid']);
            $this->service_Session->destroyLogin();
            exit();
        }
        
        $this->model_Groups->editUserLevel($obj_User->getID(), array(
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
            $this->service_Logs->securityLog('Call to unknown user ' . $this->post['userid']);
            $this->service_Session->destroyLogin();
            exit();
        }
        
        $this->model_Login->loginAs($this->post['userid']);
    }

    /**
     * Generates the user overview
     */
    protected function index()
    {
        $a_users = $this->model_User->getUsers(0);
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
    }

    /**
     * Generates the search overview
     */
    protected function search()
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
    protected function view()
    {
        try {
            $obj_User = $this->model_User->get($this->get['userid']);
        } catch (\Exception $e) {
            $this->service_Logs->securityLog('Call to unknown user ' . $this->get['userid']);
            $this->service_Session->destroyLogin();
            exit();
        }
        
        $this->helper_view->setModus('view');
        $this->helper_view->setData($obj_User);
        $this->helper_view->run();
    }

    /**
     * Generates the edit screen
     */
    protected function editScreen()
    {
        try {
            $obj_User = $this->model_User->get($this->get['userid']);
        } catch (\Exception $e) {
            $this->service_Logs->securityLog('Call to unknown user ' . $this->get['userid']);
            $this->service_Session->destroyLogin();
            exit();
        }
        
        $this->helper_view->setModus('edit');
        $this->helper_view->setData($obj_User);
        $this->helper_view->run();
    }

    /**
     * Generates the add screen
     */
    protected function addScreen()
    {
        $this->helper_view->run();
    }

    /**
     * Edits the given user
     */
    protected function edit()
    {
        try {
            $obj_User = $this->model_User->get($this->post['userid']);
        } catch (\Exception $e) {
            $this->service_Logs->securityLog('Call to unknown user ' . $this->post['userid']);
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
            if ($obj_User->getEmail() != $this->post['email']) {
                $this->service_Mailer->adminPasswordReset($obj_User->getUsername(), $obj_User->getEmail(), $this->post['password']);
            }
            $this->service_Mailer->adminPasswordReset($obj_User->getUsername(), $this->post['email'], $this->post['password']);
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
        $this->service_Mailer->adminAdd($this->post['username'], $this->post['password'], $this->post['email']);
    }

    /**
     * Deletes the given user
     */
    protected function delete()
    {
        if ($this->post['userid'] == USERID)
            exit();
        
        try {
            $obj_User = $this->model_User->get($this->post['userid']);
        } catch (\Exception $e) {
            $this->service_Logs->securityLog('Call to unknown user ' . $this->post['userid']);
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
    protected function checkUsername($s_username)
    {
        return $this->model_User->checkUsername($s_username);
    }

    /**
     * Checks if the email is available
     *
     * @return boolean if the email is available
     */
    protected function checkEmail($s_email)
    {
        return $this->model_User->checkEmail($s_email);
    }
}