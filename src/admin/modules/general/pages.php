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
 * Admin page rights configuration class
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 2.0
 */
class Pages extends \admin\AdminController
{
  /**
   *
   * @var \youconix\core\models\PrivilegeController
   */
  private $privilegeController;

  /**
   *
   * @var \youconix\core\models\Groups
   */
  private $groups;

  /**
   *
   * @var \youconix\core\services\FileHandler
   */
  private $file;

  /**
   *
   * @var \youconix\core\helpers\PageRightTree
   */
  private $tree;

  /**
   * Starts the class Pages
   *
   * @param \Request $request
   * @param \Language $language
   * @param \Output $template
   * @param \Logger $logs
   * @param \youconix\core\services\Headers $headers
   * @param \youconix\core\models\PrivilegeController $privilegeController
   * @param \youconix\core\models\Groups $groups
   * @param \youconix\core\services\FileHandler $file
   * @param \youconix\core\helpers $tree
   */
  public function __construct(\Request $request, \Language $language,
                              \Output $template, \Logger $logs,
                              \youconix\core\services\Headers $headers,
                              \youconix\core\models\PrivilegeController $privilegeController,
                              \youconix\core\models\Groups $groups,
                              \youconix\core\services\FileHandler $file,
                              \youconix\core\helpers\PageRightTree $tree)
  {
    parent::__construct($request, $language, $template, $logs, $headers);

    $this->privilegeController = $privilegeController;
    $this->groups = $groups;
    $this->file = $file;
    $this->tree = $tree;
  }

  /**
   * Inits the class Pages
   */
  protected function init()
  {
    $this->init_get = array(
        'url' => 'string-DB'
    );
    $this->init_post = array(
        'url' => 'string-DB',
        'rights' => 'int',
        'group' => 'int',
        'view' => 'string-DB',
        'id' => 'int'
    );

    parent::init();
  }

  /**
   * Shows the index
   *
   * @return \Output
   */
  public function index()
  {
    $a_files = $this->privilegeController->getPages();
    $this->tree->generate($a_files[1]);

    $a_output = [
        'pageTitle' => t('system/general/pages/pageTitle'),
        'tree' => $this->tree,
    ];

    $template = $this->createView('general/pages/index', $a_output);
    return $template;
  }

  /**
   * Shows the edit text
   *
   * @return array
   */
  private function viewEditText()
  {
    $a_output = [
        'buttonBack' => t('system/buttons/back'),
        'buttonDelete' => t('system/buttons/delete'),
        'delete' => t('system/buttons/delete'),
        'save' => t('system/buttons/save'),
        'add' => t('system/buttons/add'),
        'generalRightsHeader' => t('system/general/pages/generalRightsHeader'),
        'groupLabel' => t('system/general/pages/group'),
        'groupDefault' => t('system/general/pages/groupDefault'),
        'accessLevelLabel' => t('system/general/pages/accessLevel'),
        'viewRightsTitle' => t('system/general/pages/viewRights'),
        'viewRightsDefault' => t('system/general/pages/viewRightsDefault'),
        'pageTitle' => t('system/general/pages/editPageTitle'),
        'viewLabel' => t('system/general/pages/view')
    ];

    return $a_output;
  }

  /**
   * Shows the edit page
   *
   * @param string  $page
   * @return \Output
   */
  public function view($page)
  {
    $s_page = $this->prepareUrl($page);
    $a_rights = $this->privilegeController->getRightsForPage($s_page);

    $a_output = array_merge($this->viewEditText(),
        [
        'url' => $s_page,
        'reset' => t('system/buttons/reset'),
        'name' => $a_rights['page'],
        'template_rights' => []
    ]);


    $a_output['groups'] = $this->getGroupList($a_rights['general']['groupID']);
    $a_output['groups2'] = $this->getGroupList(- 1);
    $a_output['pageRight'] = $this->getAccessList($a_rights['general']['minLevel']);
    $a_output['templateRights'] = $this->getAccessList(- 1);

    foreach ($a_rights['commands'] as $a_right) {
      $a_output['template_rights'][] = [
          'id' => $a_right['id'],
          'command' => $a_right['command'],
          'group' => $this->groups->getGroup($a_right['groupID'])
              ->getName(),
          'level' => t('system/rights/level_'.$a_right['minLevel'])
      ];
    }

    $template = $this->createView('general/pages/view', $a_output);
    return $template;
  }

  /**
   * Shows the group select box
   * 
   * @param int $i_default    The default ID
   * @return array
   */
  private function getGroupList($i_default)
  {
    $a_groups = $this->groups->getGroups();
    $a_data = [];

    foreach ($a_groups as $model_Group) {
      ($model_Group->getID() == $i_default) ? $s_selected = 'selected="selected"'
                : $s_selected = '';

      $a_data[] = [
          'value' => $model_Group->getID(),
          'selected' => $s_selected,
          'text' => $model_Group->getName()
      ];
    }

    return $a_data;
  }

  /**
   * Shows the access list select box
   * 
   * @param int $i_default    The default ID
   * @return array
   */
  private function getAccessList($i_default)
  {
    $a_data = [];

    for ($i = 0; $i <= 2; $i ++) {
      ($i == $i_default) ? $s_selected = 'selected="selected"' : $s_selected = '';
      $a_data[] = [
          'value' => $i,
          'selected' => $s_selected,
          'text' => t('system/rights/level_'.$i)
      ];
    }

    return $a_data;
  }

  /**
   * Edits the page rights
   */
  public function edit()
  {
    if (!$this->post->validate(array(
            'url' => 'required',
            'rights' => 'required|set:0,1,2',
            'group' => 'required|type:int|min:0'
        ))) {
      return;
    }

    /* Check group */
    $a_groups = $this->groups->getGroups();
    if (!array_key_exists($this->post['group'], $a_groups)) {
      return;
    }

    $s_url = $this->prepareUrl($this->post->get('url'));

    $this->privilegeController->changePageRights($s_url,
        $this->post->get('rights'), $this->post->get('group'));
  }

  /**
   * Deletes the page.
   */
  public function delete()
  {
    if (!$this->post->validate(array(
            'url' => 'required'
        ))) {
      return;
    }

    $s_fileURI = NIV.$this->post['url'];
    $s_templatesURI = $this->config->getStylesDir().str_replace('.php', '',
            $this->post->get('url'));

    $s_url = $this->prepareUrl($this->post->get('url'));
    $this->privilegeController->deletePageRights($s_url);
    if ($this->file->exists($s_fileURI)) {
      $this->file->deleteFile(NIV.$this->post->get('url'));
    }
    if ($this->file->exists($s_templatesURI)) {
      $this->file->deleteDirectory($s_templatesURI);
    }
  }

  /**
   * Adds a view access right
   *
   * @return \Output
   */
  public function addView()
  {
    if (!$this->post->validate(array(
            'url' => 'required',
            'rights' => 'required|set:0,1,2',
            'group' => 'required|type:int|min:0',
            'view' => 'required'
        ))) {
      return;
    }

    $s_url = $this->prepareUrl($this->post->get('url'));
    $i_id = $this->privilegeController->addViewRight($s_url,
        $this->post->get('group'), $this->post->get('view'),
        $this->post->get('rights'));
    $group = $this->groups->getGroup($this->post->get('group'));

    $this->template->set('id', $i_id);
    $this->template->set('view', $this->post->get('view'));
    $this->template->set('level', $this->post->get('rights'));
    $this->template->set('deleteText', t('system/buttons/delete'));
    $this->template->set('group', $group->getName());
  }

  /**
   * Deletes the view access right
   */
  public function deleteView()
  {
    if (!$this->post->validate(array(
            'id' => 'required|type:int|min:1'
        ))) {
      return;
    }

    $this->privilegeController->deleteViewRight($this->post->get('id'));
  }

  /**
   * Removes the trailing slashes
   *
   * @param string $s_url The page url
   * @return string
   */
  private function prepareUrl($s_url)
  {
    while ((substr($s_url, 0, 1) == '/') || (substr($s_url, 0, 1) == '\\')) {
      $s_url = substr($s_url, 1);
    }

    return $s_url;
  }

  /**
   * Resets the page access rights
   */
  public function reset()
  {
    if (!$this->post->validate(array(
            'url' => 'required'
        ))) {
      return;
    }

    $s_url = $this->prepareUrl($this->post->get('url'));
    $this->privilegeController->deletePageRights($s_url);
  }
}