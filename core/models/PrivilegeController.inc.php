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
 * Controller for the page privileges
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 */
class PrivilegeController extends \core\models\Model
{

    /**
     *
     * @var \core\services\FileHandler
     */
    protected $file;

    /**
     *
     * @var \Config
     */
    protected $config;

    protected $a_skipDirs = array();

    /**
     * PHP5 constructor
     *
     * @param \Builder $builder
     *            The query builder
     * @param \core\services\Validation $service_Validation
     *            The validation service
     * @param \core\services\FileHandler $file
     *            The file service
     */
    public function __construct(\Builder $builder, \core\services\Validation $service_Validation, \core\services\FileHandler $file, \Config $config)
    {
        parent::__construct($builder, $service_Validation);
        
        $this->file = $file;
        $this->config = $config;
        
        $s_root = $_SERVER['DOCUMENT_ROOT'] . $config->getBase();
        
        $a_skipDirs = array(
            '.git',
            'core',
            'emailImages',
            'emails',
            'files',
            'fonts',
            'includes',
            'install',
            'images',
            'js',
            'language',
            'vendor',
            'openID',
            'stats',
            'tests',
            'admin' . DIRECTORY_SEPARATOR . 'data',
            'styles',
            'router.php',
            'routes.php'
        );
        foreach ($a_skipDirs as $item) {
            $this->a_skipDirs[] = $s_root . $item;
        }
    }

    /**
     * Returns all the controlers
     *
     * @return array The controllers and the site root
     */
    public function getPages()
    {
        $s_root = $_SERVER['DOCUMENT_ROOT'];
        $s_base = $this->config->getBase();
        $s_root .= $s_base;
        
        $a_pages = $this->file->readFilteredDirectory($s_root, $this->a_skipDirs, '\.php');
        
        return array(
            $s_root,
            $a_pages
        );
    }

    /**
     * Returns the rights for the given page
     *
     * @param string $s_page
     *            The page
     * @return array The access rights
     */
    public function getRightsForPage($s_page)
    {
        $a_rights = array(
            'page' => $s_page,
            'general' => array(
                'id' => - 1,
                'groupID' => - 1,
                'minLevel' => - 2
            ),
            'commands' => array()
        );
        
        /* Check general rights */
        $this->builder->select('group_pages', '*')
            ->order('groupID')
            ->getWhere()
            ->addOr(array(
            'page',
            'page'
        ), array(
            's',
            's'
        ), array(
            $s_page,
            substr($s_page, 1)
        ));
        $database = $this->builder->getResult();
        
        if ($database->num_rows() > 0) {
            $a_data = $database->fetch_assoc();
            $a_rights = array(
                'page' => $s_page,
                'general' => $a_data[0],
                'commands' => array()
            );
        }
        
        $this->builder->select('group_pages_command', '*')
            ->order('groupID')
            ->getWhere()
            ->addAnd('page', 's', $s_page);
        $database = $this->builder->getResult();
        
        if ($database->num_rows() > 0) {
            $a_rights['commands'] = $database->fetch_assoc();
        }
        
        return $a_rights;
    }

    /**
     * Changes the page rights
     *
     * @param string $s_page
     *            The page
     * @param int $i_rights
     *            The minimun access rights
     * @param int $i_group
     *            The group ID
     */
    public function changePageRights($s_page, $i_rights, $i_group)
    {
        $this->builder->update('group_pages', array(
            'groupID',
            'minLevel'
        ), array(
            'i',
            'i'
        ), array(
            $i_group,
            $i_rights
        ));
        $this->builder->getWhere()->addOr(array(
            'page',
            'page'
        ), array(
            's',
            's'
        ), array(
            $s_page,
            substr($s_page, 1)
        ));
        $database = $this->builder->getResult();
        
        if ($database->affected_rows() == 0) {
            $this->addPageRights($s_page, $i_rights, $i_group);
        }
    }

    /**
     * Adds the page rights
     *
     * @param string $s_page
     *            The page
     * @param int $i_rights
     *            The minimun access rights
     * @param int $i_group
     *            The group ID
     */
    public function addPageRights($s_page, $i_rights, $i_group)
    {
        $this->builder->insert('group_pages', array(
            'groupID',
            'minLevel',
            'page'
        ), array(
            'i',
            'i',
            's'
        ), array(
            $i_group,
            $i_rights,
            $s_page
        ));
        $this->builder->getResult();
    }

    /**
     * Removes the page`s rights from the database.
     * In essence, it makes it forget about the page.
     *
     * @param string $s_page
     *            The URL of the particular page to be forgotten about.
     *            
     * @author Roxanna Lugtigheid
     */
    public function deletePageRights($s_page)
    {
        try {
            $this->builder->transaction();
            
            $this->builder->delete('group_pages');
            $this->builder->getWhere()->addOr(array(
                'page',
                'page'
            ), array(
                's',
                's'
            ), array(
                $s_page,
                substr($s_page, 1)
            ));
            $this->builder->getResult();
            
            $this->builder->delete('group_pages_command');
            $this->builder->getWhere()->addOr(array(
                'page',
                'page'
            ), array(
                's',
                's'
            ), array(
                $s_page,
                substr($s_page, 1)
            ));
            $this->builder->getResult();
            
            $this->builder->commit();
        } catch (\DBException $e) {
            $this->builder->rollback();
        }
    }

    /**
     * Adds the view specific rights
     *
     * @param string $s_page
     *            The URL of the particular page
     * @param int $i_group
     *            The group ID
     * @param string $s_command
     *            The view name
     * @param int $i_rights
     *            The minimal access level
     * @return int The new ID, -1 on an error
     */
    public function addViewRight($s_page, $i_group, $s_command, $i_rights)
    {
        try {
            $this->builder->transaction();
            
            $this->builder->select('group_pages_command', 'id')
                ->getWhere()
                ->addAnd(array(
                'page',
                'command',
                'groupID'
            ), array(
                's',
                's',
                'i'
            ), array(
                $s_page,
                $s_command,
                $i_group
            ));
            $database = $this->builder->getResult();
            if ($database->num_rows() > 0) {
                $i_id = $database->result(0, 'id');
                $this->builder->update('group_pages_command', 'minLevel', 'i', $i_rights);
                $this->builder->getWhere()->addAnd(array(
                    'page',
                    'command'
                ), array(
                    's',
                    's'
                ), array(
                    $s_page,
                    $s_command
                ));
                $this->builder->getResult();
            } else {
                $this->builder->insert('group_pages_command', array(
                    'page',
                    'command',
                    'minLevel',
                    'groupID'
                ), array(
                    's',
                    's',
                    'i',
                    'i'
                ), array(
                    $s_page,
                    $s_command,
                    $i_rights,
                    $i_group
                ));
                $database = $this->builder->getResult();
                $i_id = $database->getId();
            }
            
            $this->builder->commit();
            
            return $i_id;
        } catch (\DBException $e) {
            $this->builder->rollback();
            return - 1;
        }
    }

    /**
     * Deletes the view specific rights
     *
     * @param int $i_id
     *            The rights ID
     */
    public function deleteViewRight($i_id)
    {
        $this->builder->delete('group_pages_command')
            ->getWhere()
            ->addAnd('id', 'i', $i_id);
        $this->builder->getResult();
    }
}