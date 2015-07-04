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

    protected $service_File;

    protected $a_skipDirs;

    /**
     * PHP5 constructor
     *
     * @param \Builder $builder
     *            The query builder
     * @param \core\services\Validation $service_Validation
     *            The validation service
     * @param \core\services\File $service_File
     *            The file service
     */
    public function __construct(\Builder $builder, \core\services\Validation $service_Validation, \core\services\File $service_File)
    {
        parent::__construct($builder, $service_Validation);
        
        $this->service_File = $service_File;
        
        $this->a_skipDirs = array(
            NIV . DIRECTORY_SEPARATOR . 'core',
            NIV . DIRECTORY_SEPARATOR . 'emailImages',
            NIV . DIRECTORY_SEPARATOR . 'emails',
            NIV . DIRECTORY_SEPARATOR . 'errors',
            NIV . DIRECTORY_SEPARATOR . 'files',
            NIV . DIRECTORY_SEPARATOR . 'fonts',
            NIV . DIRECTORY_SEPARATOR . 'includes',
            NIV . DIRECTORY_SEPARATOR . 'install',
            NIV . DIRECTORY_SEPARATOR . 'js',
            NIV . DIRECTORY_SEPARATOR . 'language',
            NIV . DIRECTORY_SEPARATOR . 'lib',
            NIV . DIRECTORY_SEPARATOR . 'openID',
            NIV . DIRECTORY_SEPARATOR . 'stats',
            NIV . DIRECTORY_SEPARATOR . 'tests',
            NIV . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'data',
            NIV . DIRECTORY_SEPARATOR . 'router.php',
            NIV . DIRECTORY_SEPARATOR . 'routes.php'
        );
    }

    public function getPages()
    {
        $a_pages = $this->readDir(NIV);
        
        return $a_pages;
    }

    protected function readDir($s_dir)
    {
        $a_filesRaw = $this->service_File->readDirectory($s_dir, false, true);
        $a_files = array();
        
        foreach ($a_filesRaw as $s_file) {
            if (in_array($s_dir . DIRECTORY_SEPARATOR . $s_file, $this->a_skipDirs))
                continue;
            
            if (is_dir($s_dir . DIRECTORY_SEPARATOR . $s_file)) {
                $a_dir = $this->readDir($s_dir . DIRECTORY_SEPARATOR . $s_file);
                
                if (count($a_dir) > 0) {
                    $a_files[$s_file] = $a_dir;
                }
            } else {
                if (substr($s_file, - 3) != 'php')
                    continue;
                
                $a_files[] = array(
                    str_replace(array(
                        '//',
                        '../',
                        './',
                        '..'
                    ), array(
                        '',
                        '',
                        '',
                        ''
                    ), $s_dir . DIRECTORY_SEPARATOR . $s_file),
                    $s_file
                );
            }
        }
        
        return $a_files;
    }

    public function getRightsForPage($s_page)
    {
        $a_rights = array(
            'page' => $s_page,
            'general' => array(
                'id' => - 1,
                'groupID' => 1,
                'minLevel' => - 1
            ),
            'commands' => array()
        );
        
        /* Check general rights */
        $this->builder->select('group_pages', '*')
            ->getWhere()
            ->addAnd('page', 's', $s_page);
        $database = $this->builder->getResult();
        
        if ($database->num_rows() > 0) {
            $a_data = $database->fetch_assoc();
            $a_rights = array(
                'page' => $s_page,
                'general' => $a_data[0],
                'commands' => array()
            );
            
            $this->builder->select('group_pages_command', '*')
                ->getWhere()
                ->addAnd('page', 's', $s_page);
            $database = $this->builder->getResult();
            
            if ($database->num_rows() > 0) {
                $a_rights['commands'] = $database->fetch_assoc();
            }
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
        $this->builder->getWhere()->addAnd('page', 's', $s_page);
        $this->builder->getResult();
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
            $this->builder->getWhere()->addAnd('page', 's', $s_page);
            $this->builder->getResult();
            
            $this->builder->delete('group_pages_command');
            $this->builder->getWhere()->addAnd('page', 's', $s_page);
            $this->builder->getResult();
            
            $this->builder->commit();
        } catch (\DBException $e) {
            $this->builder->rollback();
        }
    }

    public function addViewRight($s_page, $s_command, $i_rights)
    {}

    public function deleteViewRight($s_page, $s_command)
    {
        // Remove from DB
    }
}