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
 * Admin group configuration class
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 */
class Groups extends \admin\AdminController
{
  /**
   *
   * @var \core\repositories\Groups
   */
  private $groups;
  private $a_systemGroups = [0, 1];

  /**
   * Groups constructor
   *
   * @param \Request $request
   * @param \Language $language
   * @param \Output $template
   * @param \Logger $logs
   * @param \youconix\core\services\Headers $headers
   * @param \youconix\core\repositories\Groups $groups
   */
  public function __construct(\Request $request, \Language $language,
                              \Output $template, \Logger $logs,
                              \youconix\core\services\Headers $headers,
                              \youconix\core\repositories\Groups $groups)
  {
    parent::__construct($request, $language, $template, $logs, $headers);

    $this->groups = $groups;
  }

  /**
   * Inits the class Groups
   */
  protected function init()
  {
    $this->init_get = [
        'id' => 'int'
    ];

    $this->init_post = [
        'id' => 'int',
        'name' => 'string-DB',
        'description' => 'string-DB',
        'default' => 'int',
        'id' => 'int'
    ];

    parent::init();
  }

  /**
   * Generates the group overview
   *
   * @return \Output
   */
  public function index()
  {
    $a_output = [
        'groupTitle' => t('system/admin/groups/groups'),
        'headerID' => t('system/admin/groups/id'),
        'buttonDelete' => t('system/buttons/delete'),
        'addButton' => t('system/buttons/add'),
        'groups' => []
    ];

    $a_output = $this->setHeader($a_output);
    $a_groups = $this->groups->getGroups();

    $s_yes = t('system/admin/users/yes');
    $s_no = t('system/admin/users/no');

    foreach ($a_groups as $obj_group) {
      $a_output['groups'][] = [
          'id' => $obj_group->getID(),
          'name' => $obj_group->getName(),
          'description' => $obj_group->getDescription(),
          'default' => ($obj_group->isDefault() ? $s_yes : $s_no)
      ];
    }

    $template = $this->createView('general/groups/index', $a_output);
    return $template;
  }

  /**
   * Displays the group
   *
   * @param int $id
   * @return \Output
   */
  public function view($id)
  {
    $obj_group = $this->getGroup($id);

    if ($obj_group->isDefault()) {
      $s_automatic = t('system/yes');
    } else {
      $s_automatic = t('system/no');
    }

    $a_output = [
        'nameDefault' => $obj_group->getName(),
        'descriptionDefault' => $obj_group->getDescription(),
        'automatic' => $s_automatic,
        'id' => $id,
        'groupTitle' => t('system/admin/groups/headerView').' '.$obj_group->getName(),
        'buttonBack' => t('system/buttons/back'),
        'buttonDelete' => t('system/buttons/delete'),
        'buttonEdit' => t('system/buttons/edit'),
        'memberlistTitle' => t('system/admin/groups/memberlist'),
        'editDisabled' => '',
        'userlist' => []
    ];

    $a_output = $this->setHeader($a_output);

    if (in_array($id, $this->a_systemGroups)) {
      $a_output['editDisabled'] = 'style="color:grey; text-decoration: line-through; cursor:auto"';
    }

    /* Display users */
    $a_users = $obj_group->getMembersByGroup();
    foreach ($a_users as $a_user) {
      $a_output['userlist'][] = [
          'userid' => $a_user['id'],
          'user' => $a_user['username'],
          'level' => t('system/rights/level_'.$a_user['level'])
      ];
    }

    $template = $this->createView('general/groups/view', $a_output);
    return $template;
  }

  /**
   * Shows the edit screen
   *
   * @param int $id
   * @return \Output
   */
  public function editScreen($id)
  {
    $obj_group = $this->getGroup($id);

    $a_output = [
        'nameDefault' => $obj_group->getName(),
        'descriptionDefault' => $obj_group->getDescription(),
        'automatic' => '',
        'id' => $id,
        'groupTitle' => t('system/admin/groups/headerView').' '.$obj_group->getName(),
        'buttonBack' => t('system/buttons/back'),
        'buttonDelete' => t('system/buttons/delete'),
        'buttonEdit' => t('system/buttons/edit'),
        'buttonCancel' => t('system/buttons/cancel'),
        'buttonSubmit' => t('system/buttons/save'),
        'editDisabled' => ''
    ];

    if (in_array($id, $this->a_systemGroups)) {
      $a_output['editDisabled'] = 'style="color:grey; text-decoration: line-through; cursor:auto"';
    }

    $a_output = $this->setHeader($a_output);

    if ($obj_group->isDefault()) {
      $a_output['automatic'] = 'checked="checked"';
    }

    $template = $this->createView('general/groups/editScreen', $a_output);
    return $template;
  }

  /**
   * Displays the add screen
   *
   * @return \Output
   */
  public function addScreen()
  {
    $a_output = [
        'groupTitle' => t('system/admin/groups/headerAdd'),
        'buttonCancel' => t('system/buttons/cancel'),
        'buttonSubmit' => t('system/buttons/save'),
        'buttonBack' => t('system/buttons/back')
    ];

    $a_output = $this->setHeader($a_output);

    $template = $this->createView('general/groups/addScreen', $a_output);
    return $template;
  }

  /**
   * Adds a new group
   */
  public function add()
  {
    if (!$this->post->validate(array(
            'name' => 'required',
            'description' => 'required',
            'defaultGroup' => 'required|set:0,1'
        ))) {
      $this->headers->http400();
      $this->headers->printHeaders();
      return;
    }

    $obj_Group = $this->groups->generateGroup();
    $obj_Group->setName($this->post['name']);
    $obj_Group->setDescription($this->post['description']);
    $obj_Group->setDefault(false);
    if ($this->post['defaultGroup'] == 1) {
      $obj_Group->setDefault(true);

      $obj_Group->addUsersToDefault();
    }
    $obj_Group->save();
  }

  /**
   * Deletes the given group
   */
  public function delete()
  {
    if (!$this->post->validate(array(
            'id' => 'required|min:2'
        ))) {
      $this->headers->http400();
      $this->headers->printHeaders();
      return;
    }

    /* Get group */
    $obj_Group = $this->getGroup($this->post['id']);
    $obj_Group->delete();
  }

  /**
   * Edits the group
   */
  public function edit()
  {
    if (!$this->post->validate(array(
            'name' => 'type:string|required',
            'description' => 'type:string|required',
            'default' => 'required|set:0,1',
            'id' => 'type:int:required'
        ))) {
      $this->headers->http400();
      $this->headers->printHeaders();
      return;
    }

    $obj_Group = $this->getGroup($this->post['id']);
    $obj_Group->setName($this->post['name']);
    $obj_Group->setDescription($this->post['description']);
    if ($this->post['default'] == 1 && !$obj_Group->isDefault()) {
      $obj_Group->setDefault(true);

      $obj_Group->addUsersToDefault();
    } else
    if ($this->post['default'] == 0) {
      $obj_Group->setDefault(false);
    }

    $obj_Group->persist();
  }

  /**
   * Sets the headers
   *
   * @param array $a_output
   * @return array
   */
  private function setHeader($a_output)
  {
    $a_output['headerName'] = t('system/admin/groups/name');
    $a_output['headerDescription'] = t('system/admin/groups/description');
    $a_output['headerAutomatic'] = t('system/admin/groups/standard');

    return $a_output;
  }

  /**
   * Returns the group
   *
   * @param int $id
   * @return \youconix\core\models\data\Group
   */
  private function getGroup($id)
  {
    try {
      $obj_group = $this->groups->getGroup((int) $id);
    } catch (\Exception $e) {
      $this->logger->info('Call to unknown group '.$id.'.',
          ['type' => 'securityLog']);
      $this->headers->http400();
      $this->headers->printHeaders();
      exit();
    }

    return $obj_group;
  }
}