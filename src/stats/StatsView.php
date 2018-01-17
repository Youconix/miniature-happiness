<?php

namespace stats;

class StatsView
{

  /**
   * \config
   */
  private $config;

  public function __construct(\Config $config)
  {
    $this->config = $config;
  }

  /**
   * 
   * @param \Output $template
   */
  private function head(\Output $template)
  {
    $script = '<script type="text/javascript">
    <!--
    let width;
    let height;
    let colors = screen.colorDepth;

    //IE
    if( !window.innerWidth ){
	if( !(document.documentElement.clientWidth == 0) ){
	    //strict mode
	    width = document.documentElement.clientWidth;
		    height = document.documentElement.clientHeight;
	} 
	    else{
	    //quirks mode
	    width = document.body.clientWidth;
		    height = document.body.clientHeight;
	}
    } else {
	//w3c
	width = window.innerWidth;
	    height = window.innerHeight;
    }

    document.getElementById("stats").src = "/stats/stats.php?page=' . $this->config->getUrl() . '&colors="+colors+"&width="+width+"&height="+height;
    //-->
    </script>
    ';

    $template->append('head', $script);
  }

  /**
   * 
   * @param \Output $template
   */
  public function generate(\Output $template)
  {
    $this->head($template);

    $output = '<div><img src="" id="stats" alt=""/></div>
      <noscript><div><img src="/stats/stats.php?page=' . $this->config->getUrl() . '" alt=""/></div></noscript>
	';
    $template->set('statisticsImg', $output);
  }
}
