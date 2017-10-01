<?php

namespace admin\modules\general;

/**
 * Admin user configuration class
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 */
class Users extends \admin\AdminController
{
  /**
   * @var \youconix\core\helpers\AdminUserViews
   */
  private $view;

  /**
   *
   * @var \youconix\core\repositories\User
   */
  private $user;

  /**
   *
   * @var \youconix\core\helpers\Localisation
   */
  private $localisation;

  /**
   * Starts the class Users
   *
   * @param \Request $request    
   * @param \Language $language            
   * @param \Output $template
   * @param \Logger $logs
   * @param \Headers $headers
   * @param \youconix\core\helpers\AdminUserViews $view
   * @param \youconix\core\repositories\User $user
   * @param \youconix\core\helpers\Localisation $localisation
   */
  public function __construct(
      \Request $request, 
      \Language $language,
      \Output $template, 
      \Logger $logs,
      \Headers $headers,
      \youconix\core\helpers\AdminUserViews $view,
      \youconix\core\repositories\User $user,
      \youconix\core\helpers\Localisation $localisation
      )
  {
    parent::__construct($request, $language, $template, $logs, $headers);

    $this->view = $view;
    $this->user = $user;
    $this->localisation = $localisation;
  }

  /**
   * Inits the class Users
   */
  protected function init()
  {
    $this->init_get = [
        'userid' => 'int',
        'command' => 'string',
        'email' => 'string-DB',
        'username' => 'string-DB',
        'page' => 'int'
    ];

    $this->init_post = [
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
    ];

    parent::init();
  }

  /**
   * Generates the add screen
   *
   * @return \Output
   */
  public function addScreen()
  {
    return $this->view->addScreen($this->user->createUser(), $this->post);
  }

  /**
   * Generates the user overview
   *
   * @return \Output
   */
  public function index()
  {
    $a_users = $this->user->getUsers(0);

    $a_data = [
        'userid' => USERID,
        'headerText' => t('system/admin/users/users'),
        'textAdd' => t('system/buttons/add'),
        'searchText' => t('system/admin/users/searchText'),
        'header_ID' => t('system/admin/users/id'),
        'header_username' => t('system/admin/users/username'),
        'header_email' => t('system/admin/users/email'),
        'header_loggedin' => t('system/admin/users/loggedIn'),
        'header_registration' => t('system/admin/users/registrated'),
        'users' => $a_users,
        'localistation' => $this->localisation
    ];

    $template = $this->createView('general/users/index', $a_data);
    return $template;
  }

  /**
   * Generates the search overview
   *
   * @param string  $username
   * @return \Output
   */
  public function search($username)
  {
    $a_usersRaw = $this->user->searchUser($username);
    $a_users = [];

    foreach ($a_usersRaw['data'] as $obj_user) {
      $item = [
          'id' => $obj_user->getID(),
          'username' => $obj_user->getUsername(),
          'email' => $obj_user->getEmail(),
          'loggedin' => $obj_user->lastLoggedIn(),
          'registrated' => $obj_user->getRegistrated()
      ];

      ($item['loggedin'] == 0) ? $item['loggedin'] = '-' : $item['loggedin'] = $this->localisation->dateOrTime($item['loggedin']);
      ($item['registrated'] == 0) ? $item['registrated'] = '-' : $item['registrated']
              = $this->localisation->dateOrTime($item['registrated']);

      $a_users[] = $item;
    }

    $this->createJsonView($a_users);
  }

  /**
   * Generates the edit screen
   *
   * @param int $userid
   * @return \Output
   */
  public function editScreen($userid)
  {
    $obj_User = $this->getUser($userid);

    return $this->view->editScreen($obj_User, $this->post);
  }

  /**
   * Generates the user detail view
   *
   * @param int $userid
   * @return \Output
   */
  public function view($userid)
  {
    $obj_User = $this->getUser($userid);

    return $this->view->viewScreen($obj_User);
  }

  /**
   * Adds the user access rights to the group
   */
  protected function addGroup()
  {
    $this->validate(['userid' => 'type:int|required|min:1']);

    $obj_User = $this->getUser($this->post['userid']);

    $this->groups->editUserLevel($obj_User->getID(),
        [
        $this->post['group']
        ], $this->post['level']);
  }

  /**
   * Deletes the user access rights to the group
   */
  protected function deleteGroup()
  {
    $this->validate(['userid' => 'type:int|required|min:1']);

    $obj_User = $this->getUser($this->post['userid']);

    $this->groups->editUserLevel($obj_User->getID(),
        [
        $this->posat['group']
        ], $this->post['level']);
  }

  /**
   * Logs the admin in as the given user
   */
  public function login()
  {
    $this->validate(['userid' => 'type:int|required|min:1']);

    $obj_User = $this->getUser($this->post['userid']);

    $this->login->loginAs($obj_User);
  }

  /**
   * Edits the given user
   *
   * @param int $userid
   */
  public function edit($userid)
  {
    $obj_User = $this->getUser($userid);

    $this->validate([
        'email' => 'type:string|pattern:email|required',
        'bot' => 'type:boolean|required',
        'blocked' => 'type:boolean|required',
        'password' => 'type:string|minlength:8',
        'password2' => 'type:string|minlength:8'
    ]);

    /* Check passwords */
    if ((!empty($this->post['password']))) {
      if ($this->post['password'] != $this->post['password2']) {
        $this->headers->http400();
        $this->headers->printHeaders();
        return;
      }

      $obj_User->setPassword($this->post['password'], true);
      if ($obj_User->getEmail() != $this->post['email']) {
        $this->mailer->adminPasswordReset($obj_User->getUsername(),
            $obj_User->getEmail(), $this->post['password']);
      }
      $this->mailer->adminPasswordReset($obj_User->getUsername(),
          $this->post['email'], $this->post['password']);
    }

    /* Edit user */
    $obj_User->setEmail($this->post['email']);
    $obj_User->setBot((bool) $this->post['bot']);
    $obj_User->setBlocked((bool) $this->post['blocked']);
    $obj_User->persist();
  }

  /**
   * Adds a new user to the database
   */
  public function add()
  {
    $this->validate([
        'username' => 'type:string|required',
        'email' => 'type:string|pattern:email|required',
        'bot' => 'type:boolean|required',
        'password' => 'type:string|required|minlength:8',
        'password2' => 'type:string|required|minlength:8'
    ]);

    if (!$this->user->checkUsername($this->post['username']) || !$this->checkEmail($this->post['email'])
        ||
        ($this->post['password'] != $this->post['password2'])) {
      $this->headers->http400();
      $this->headers->printHeaders();
      return;
    }

    /* Add user */
    $obj_User = $this->user->createUser();
    $obj_User->setUsername($this->post['username']);
    $obj_User->setEmail($this->post['email']);
    $obj_User->setPassword($this->post['password'], true);
    $obj_User->setBot((bool) $this->post['bot']);
    $obj_User->enableAccount();
    $obj_User->persist();

    /* Send notification email */
    $this->mailer->adminAdd($this->post['username'], $this->post['password'],
        $this->post['email']);
  }

  /**
   * Deletes the given user
   */
  public function delete()
  {
    $this->validate(['userid' => 'type:int|required|min:1']);

    if ($this->post['userid'] == USERID) {
      exit();
    }

    $obj_User = $this->getUser($this->post['userid']);

    /* Say bye bye */
    $obj_User->delete();
  }

  /**
   * Checks if the username is available
   *
   * @param string $s_username
   */
  public function checkUsername($s_username)
  {
    if ($this->user->checkUsername($s_username)) {
      echo '1';
    } else {
      echo '0';
    }
  }

  /**
   * Checks if the email is available
   *
   * @param string $s_email
   */
  public function checkEmail($s_email)
  {
    if ($this->user->checkEmail($s_email)) {
      echo '1';
    } else {
      echo '0';
    }
  }

  /**
   * Returns the user with the given ID
   * Throws a 400 bad request if the user does not exist
   *
   * @param int $userID
   * @return \youconix\core\models\data\User
   */
  private function getUser($userID)
  {
    try {
      $user = $this->user->get((int) $userID);
    } catch (\Exception $ex) {
      $this->logger->info('Call to unknown user '.$userID,
          ['type' => 'securityLog']);
      $this->headers->http400();
      $this->headers->printHeaders();
      exit();
    }

    return $user;
  }

  /**
   * Validates the post data
   * Throws a 400 bad request if the data is not valid
   *
   * @param array $a_rules
   */
  private function validate($a_rules)
  {
    if (!$this->post->validate($a_rules)) {
      $this->headers->http400();
      $this->headers->printHeaders();
      exit();
    }
  }
}