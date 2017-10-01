<?php

namespace errors;

use \youconix\core\templating\BaseController as BaseController;
use \includes\BaseLogicClass AS Layout;
use \youconix\core\services\Headers AS Headers;

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
 * Error 404 class
 *
 * @copyright Youconix
 * @author : Rachelle Scheijen
 * @version 1.0
 * @since 1.0
 */
class Error404 extends BaseController {

  /**
   *
   * @var \Language
   */
  private $language;

  /**
   *
   * @var \youconix\core\services\Headers
   */
  private $headers;

  /**
   * Constructor
   *
   * @param \Request $request
   * @param \Language $language            
   * @param \Output $template
   * @param \youconix\core\services\Headers Headers
   */
  public function __construct(\Request $request, \Language $language, \Output $template, Layout $layout,Headers $headers) {
    parent::__construct($request, $layout, $template);

    $this->language = $language;
    $this->headers = $headers;
  }

  /**
   * Displays the error
   */
  public function index(\Exception $exception = null) {
    $this->headers->http404();
    
    $a_data = [
	'title' => t('errors/error404/notFound'),
	'notice' => t('errors/error404/pageMissing'),
	'debug_notice' => ''
    ];
    
    if ( !is_null($exception) && defined('DEBUG') ){
	$a_data['debug_notice'] = $exception->getMessage().'<br>'.$exception->getTraceAsString();
    }
    
    try {
      $template = $this->createView('errors/error404/index',$a_data);
    }
    catch(\Exception $e){
      print_r($e->getMessage());
    }
    
    return $template;
  }

}
