<?php
namespace admin;

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
 * Admin settings configuration class
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 */
if (! defined('NIV')) {
    define('NIV', '../../../');
}

include (NIV . 'admin/modules/settings/settings.php');

class Cache extends \admin\Settings
{

    /**
     *
     * @var \core\services\Cache
     */
    private $service_Cache;

    /**
     * Calls the functions
     */
    protected function menu()
    {
        if (isset($this->get['command'])) {
            switch ($this->get['command']) {
                case 'cache':
                    $this->cache();
                    break;
            }
        } else 
            if (isset($this->post['command'])) {
                switch ($this->post['command']) {
                    case 'cache':
                        $this->cacheSave();
                        break;
                    
                    case 'addNoCache':
                        $this->addNoCache();
                        break;
                        
                    case 'deleteNoCache' :
                        $this->deleteNoCache();
                        break;
                }
            }
    }

    /**
     * Inits the class Settings
     */
    protected function init()
    {
        $this->init_post = array(
            'cache' => 'boolean',
            'expire' => 'int',
            'page' => 'string',
            'id' => 'int'
        );
        
        parent::init();
        
        $this->service_Cache = \Loader::Inject('\core\services\Cache');
    }

    /**
     * Loads the cache settings
     */
    private function cache()
    {
        $this->service_Template->set('cacheTitle', t('system/admin/settings/cache/title'));
        $this->service_Template->set('cacheActiveText', 'Caching geactiveerd');
        if ($this->getValue('cache/status') == 1) {
            $this->service_Template->set('cacheActive', 'checked="checked"');
        } else {
            $this->service_Template->set('cacheSettings', 'style="display:none"');
        }
        
        $this->service_Template->set('cacheExpireText', 'Cache verloop tijd in seconden');
        $this->service_Template->set('cacheExpire', $this->getValue('cache/timeout', 86400));
        
        $a_pages = $this->service_Cache->getNoCachePages();
        foreach ($a_pages as $a_page) {
            $this->service_Template->setBlock('noCache', array(
                'id' => $a_page['id'],
                'name' => $a_page['page']
            ));
        }
        
        $this->service_Template->set('delete', t('system/buttons/delete'));
        $this->service_Template->set('saveButton', t('system/buttons/save'));
        $this->service_Template->set('page', 'Pagina');
        $this->service_Template->set('addButton', t('system/buttons/add'));
    }

    /**
     * Saves the cache settings
     */
    private function cacheSave()
    {
        if (! $this->service_Validation->validate(array(
            'cache' => array(
                'required' => 1,
                'set' => array(
                    0,
                    1
                )
            ),
            'expire' => array(
                'required' => 1,
                'type' => 'int',
                'min-value' => 60
            )
        ), $this->post));
        
        $this->setValue('cache/status', $this->post['cache']);
        $this->setValue('cache/timeout', $this->post['expire']);
        $this->service_Settings->save();
    }

    private function addNoCache()
    {
        if (! $this->service_Validation->validate(array(
            'page' => array(
                'type'=> 'string',
                'required' => 1
            )
        ), $this->post)) {
            return;
        }
        
        $i_id = $this->service_Cache->addNoCachePage($this->post['page']);
        $this->service_Template->set('id',$i_id);
        $this->service_Template->set('name',$this->post['page']);
        $this->service_Template->set('delete',t('system/buttons/delete'));
    }
    
    private function deleteNoCache(){
        if (! $this->service_Validation->validate(array(
            'id' => array(
                'type'=> 'int',
                'required' => 1,
                'min'=>1
            )
        ), $this->post)) {
            return;
        }
        
        $this->service_Cache->deleteNoCache($this->post['id']);
    }
}

$obj_cache = new Cache();
unset($obj_cache);