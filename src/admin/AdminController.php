<?php

namespace admin;

abstract class AdminController
{
  /**
   *
   * @var \Request
   */
  protected $request;

  /**
   *
   * @var \Language
   */
  protected $language;

  /**
   *
   * @var \Output
   */
  protected $output;

  /**
   *
   * @var \Logger
   */
  protected $logger;

  /**
   *
   * @var \Headers
   */
  protected $headers;

  protected $init_post = array();
  protected $init_get = array();

  /**
   *
   * @var \Input
   */
  protected $post;

  /**
   *
   * @var \Input
   */
  protected $get;

  /**
   * General admin controller
   *
   * @param \Request $request
   * @param \Language $language
   * @param \Output $template
   * @param \Logger $logs
   * @param \Headers $headers
   */
  public function __construct(\Request $request, \Language $language,
                              \Output $template, \Logger $logger,\Headers $headers)
  {
    $this->request = $request;
    $this->language = $language;
    $this->output = $template;
    $this->logger = $logger;
    $this->headers = $headers;

    $this->init();
  }

  /**
   * Inits the class
   */
  protected function init()
  {
    $this->get = $this->request->get()->getAll('GET');
    $this->post = $this->request->post()->getAll('POST');
  }

  /**
   * Loads the given view into the parser
   *
   * @param string $s_view
   *            The view relative to the template-directory
   * @param array $a_data
   * 		  Data as key-value pair
   * @return \Output
   * @throws \TemplateException if the view does not exist
   * @throws \IOException if the view is not readable
   */
  protected function createView($s_view, $a_data = [])
  {
    $s_templateDir = 'admin';

    $this->output->load('admin/modules/'.$s_view, $s_templateDir);
    $this->output->setArray($a_data);

    return $this->output;
  }

  protected function createJsonView($a_data){
    $this->headers->contentType('application/json');
    $this->headers->printHeaders();
    echo(json_encode($a_data));
  }
}