<?php

namespace admin\modules\settings;

/**
 * Cache configuration class
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
   *
   * @var \youconix\core\Routes
   */
  private $routes;
  
  /**
   *
   * @var \youconix\core\ORM\EntityHelper
   */
  private $entityHelper;

  /**
   * Constructor
   *
   * @param \youconix\core\templating\AdminControllerWrapper $wrapper
   * @param \youconix\core\helpers\OnOff $onOff
   * @param \Settings $settings 
   * @param \Cache $cache            
   * @param \youconix\core\Routes $routes
   * @param \youconix\core\ORM\EntityHelper $entityHelper
   */
  public function __construct(\youconix\core\templating\AdminControllerWrapper $wrapper,
			      \youconix\core\helpers\OnOff $onOff, \Settings $settings, 
			      \Cache $cache, \youconix\core\Routes $routes,
			      \youconix\core\ORM\EntityHelper $entityHelper)
  {
    parent::__construct($wrapper, $onOff, $settings);

    $this->cache = $cache;
    $this->routes = $routes;
    $this->entityHelper = $entityHelper;
  }
  
  /**
   * Inits the class Settings
   */
  protected function init()
  {
    $this->init_post = [
	'cacheActive' => 'boolean',
	'expire' => 'int',
	'page' => 'string',
	'id' => 'int'
    ];

    parent::init();
  }

  /**
   * Loads the cache settings
   * @Route("/admin/settings/cache", name="admin_settings_cache")
   */
  public function cache()
  {
    $template = $this->createView('admin/modules/settings/cache/cache');
    
    $cacheActive = $this->createSlider('cacheActive', $this->getValue('cache/status'));
    
    $template->set('cacheTitle', $this->getText('cache', 'title'));
    $template->set('cacheActiveText', $this->getText('cache', 'cacheActive'));
    $template->set('cacheActive', $cacheActive);
    $template->set('cacheExpireText',$this->getText('cache', 'cacheExpire'));
    $template->set('expireError', $this->getText('cache', 'expireError'));
    $template->set('cacheExpire', $this->getValue('cache/timeout', 86400));
    $template->set('saveButton', t('system/buttons/save'));
    
    $this->setDefaultValues($template);
    
    return $template;
  }

  /**
   * Saves the cache settings
   * @Route("/admin/settings/cache/save", name="admin_settings_cache_save")
   */
  public function cacheSave()
  {
    $post = $this->getRequest()->post();
    if (!$post->validate([
	    'cacheActive' => 'required|set:0,1',
	    'expire' => 'required|type:int|min:60'
	])) {
      $this->badRequest();
      return;
    }

    $this->setValue('cache/status', $post->get('cacheActive'));
    $this->setValue('cache/timeout', $post->get('expire'));
    $this->settings->save();
  }
  
  /**
   * @Route("/admin/settings/cache/no/screen", name="admin_settings_cache_exclude")
   */
  public function noCacheScreen()
  {
    $template = $this->createView('admin/modules/settings/cache/no_cache');
    
    $template->set('cacheTitle', $this->getText('cache', 'excludeCache'));
    
    $pages = $this->cache->getNoCachePages();
    $addresses = $this->routes->getAllAddresses();
    
    $noCache = [];
    foreach ($pages as $page) {
      $noCache[] = [
	  'id' => $page['id'],
	  'name' => $page['page']
      ];
      
      $addresses = array_diff($addresses, [$page['page']]);
    }
    $template->set('noCache', $noCache);
    
    asort($addresses);
    $template->set('addresses', $addresses);
    $template->set('excludeTitle', $this->getText('cache', 'excludeTitle'));
    $template->set('addExcludeTitle', $this->getText('cache', 'addExcludeTitle'));
    $template->set('delete', t('system/buttons/delete'));
    $template->set('saveButton', t('system/buttons/save'));
    $template->set('page', t('system/settings/cache/page'));
    $template->set('addButton', t('system/buttons/add'));
    
    $this->setDefaultValues($template);
    
    return $template;
  }

  /**
   * Adds a no cache item
   * @Route("/admin/settings/cache/no", name="admin_settings_no_cache")
   */
  public function addNoCache()
  {
    $post = $this->getRequest()->post();
    if (!$post->validate([
      'page' => 'required|type:string'
      ])) {
      $this->badRequest();
      return;
    }

    $id = $this->cache->addNoCachePage($post->get('page'));
    $this->createJsonResponse([
	'id' => $id,
	'name' => $post->get('page')
    ]);
  }

  /**
   * Deletes the no cache item
   * @Route("/admin/settings/cache/delete", name="admin_settings_cache_delete")
   */
  public function deleteNoCache()
  {
    $post = $this->getRequest()->post();
    if (!$post->validate([
	    'id' => 'required|type:int|min:1'
	])) {
      return;
    }

    $this->cache->deleteNoCache($post->get('id'));
  }
  
  /**
   * @Route("/admin/settings/cache/empty/screen", name="admin_settings_cache_empty_screen")
   */
  public function emptyCacheScreen()
  {
    $template = $this->createView('admin/modules/settings/cache/empty_cache');
    
    $template->set('cacheTitle', $this->getText('cache', 'emptyCache'));
    $template->set('cacheRemovalProcess', $this->getText('cache', 'emptyCacheProgress'));
    $template->set('cacheRemovalComplete', $this->getText('cache', 'emptyCacheComplete'));
    $template->set('emptyButton', $this->getText('cache', 'emptyCacheButton'));
    
    $this->setDefaultValues($template);
    
    return $template;
  }
  
  /**
   * @Route("/admin/settings/cache/empty", name="admin_settings_cache_empty")
   */
  public function emptyCache()
  {
    $this->cache->clearSiteCache();
    $this->cache->cleanLanguageCache();
    $this->entityHelper->dropCache();
    $this->routes->dropCache();
  }
}
