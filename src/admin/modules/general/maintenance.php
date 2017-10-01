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
 * Admin maintenance class
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 */
class Maintenance extends \admin\AdminController
{
  /**
   *
   * @var \youconix\core\services\Maintenance
   */
  private $maintenance;

  /**
   * Starts the class Groups
   *
   * @param \Request $request
   * @param \Language $language
   * @param \Output $template
   * @param \Logger $logs
   * @param \youconix\core\services\Headers $headers
   * @param \youconix\core\services\Maintenance    $maintenance
   */
  public function __construct(\Request $request, \Language $language,
                              \Output $template, \Logger $logs,
                              \youconix\core\services\Headers $headers,
                              \youconix\core\services\Maintenance $maintenance)
  {
    parent::__construct($request, $language, $template, $logs, $headers);

    $this->maintenance = $maintenance;
  }

  /**
   * Inits the class Maintenance
   */
  protected function init()
  {
    $this->init_post = [
        'action' => 'string'
    ];

    parent::init();
  }

  /**
   * Generates the action menu
   *
   * @return \Output
   */
  public function index()
  {
    $a_output = [
      'moduleTitle' => t('system/admin/general/maintenance'),
      'checkDatabase' => t('system/admin/maintenance/checkDatabase'),
      'optimizeDatabase' => t('system/admin/maintenance/optimizeDatabase'),
      'cleanStatsYear' => t('system/admin/maintenance/stats'),
      'backup' => t('system/admin/maintenance/createBackup'),
      'ready' => t('system/admin/maintenance/ready')
    ];

    $template = $this->createView('general/maintenance/index', $a_output);
    return $template;
  }

  /**
   * Performs the maintenance action
   *
   * @param string $s_action
   *            The action to take
   */
  public function performAction($s_action)
  {
    $bo_result = false;

    switch ($s_action) {
      case 'checkDatabase':
        $bo_result = $this->maintenance->checkDatabase();
        break;

      case 'optimizeDatabase':
        $bo_result = $this->maintenance->optimizeDatabase();
        break;

      case 'cleanStats':
        $bo_result = $this->maintenance->cleanStatsYear();
        break;
    }

    if ($bo_result) {
      echo '1';
    } else {
      echo '0';
    }
  }
}