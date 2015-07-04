<?php
namespace admin\modules\settings;

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
class Cache extends \admin\modules\settings\Settings
{

    /**
     *
     * @var \Cache
     */
    private $cache;

    /**
     * Constructor
     *
     * @param \Input $Input            
     * @param \Config $config            
     * @param \Language $language            
     * @param \Output $template            
     * @param \Logger $logs            
     * @param \Settings $settings            
     * @param \Cache $cache            
     */
    public function __construct(\core\Input $Input, \Config $config, \Language $language, \Output $template, \Logger $logs, \Settings $settings, \Cache $cache)
    {
        parent::__construct($Input, $config, $language, $template, $logs, $settings);
        
        $this->cache = $cache;
    }

    /**
     * Routes the controller
     *
     * @see Routable::route()
     */
    public function route($s_command)
    {
        if ($_SERVER["REQUEST_METHOD"] != 'POST') {
            $this->cache();
        } else
            switch ($s_command) {
                case 'cache':
                    $this->cacheSave();
                    break;
                
                case 'addNoCache':
                    $this->addNoCache();
                    break;
                
                case 'deleteNoCache':
                    $this->deleteNoCache();
                    break;
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
    }

    /**
     * Loads the cache settings
     */
    private function cache()
    {
        $this->template->set('cacheTitle', t('system/admin/settings/cache/title'));
        $this->template->set('cacheActiveText', 'Caching geactiveerd');
        if ($this->getValue('cache/status') == 1) {
            $this->template->set('cacheActive', 'checked="checked"');
        } else {
            $this->template->set('cacheSettings', 'style="display:none"');
        }
        
        $this->template->set('cacheExpireText', 'Cache verloop tijd in seconden');
        $this->template->set('cacheExpire', $this->getValue('cache/timeout', 86400));
        
        $a_pages = $this->cache->getNoCachePages();
        foreach ($a_pages as $a_page) {
            $this->template->setBlock('noCache', array(
                'id' => $a_page['id'],
                'name' => $a_page['page']
            ));
        }
        
        $this->template->set('delete', t('system/buttons/delete'));
        $this->template->set('saveButton', t('system/buttons/save'));
        $this->template->set('page', 'Pagina');
        $this->template->set('addButton', t('system/buttons/add'));
    }

    /**
     * Saves the cache settings
     */
    private function cacheSave()
    {
        if (! $this->post->validate(array(
            'cache' => 'required|set:0,1',
            'expire' => 'required|type:int|min:60'
        ))) {
            return;
        }
        
        $this->setValue('cache/status', $this->post['cache']);
        $this->setValue('cache/timeout', $this->post['expire']);
        $this->service_Settings->save();
    }

    private function addNoCache()
    {
        if (! $this->post->validate(array(
            'page' => 'required|type:string'
        ))) {
            return;
        }
        
        $i_id = $this->cache->addNoCachePage($this->post['page']);
        $this->template->set('id', $i_id);
        $this->template->set('name', $this->post['page']);
        $this->template->set('delete', t('system/buttons/delete'));
    }

    private function deleteNoCache()
    {
        if (! $this->post->validate(array(
            'id' => 'required|type:int|min:1'
        ))) {
            return;
        }
        
        $this->cache->deleteNoCache($this->post['id']);
    }
}