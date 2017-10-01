<?php
$s_stats = '<div><img src="" id="stats" alt=""/></div>
<script type="text/javascript">
<!--
var width;
var height;
var colors = screen.colorDepth;
		
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
		
document.getElementById("stats").src = "{NIV}stats/stats.php?page=' . $this->config->getPage() . '&colors="+colors+"&width="+width+"&height="+height;
//-->
</script>
	
<noscript><div><img src="{NIV}stats/stats.php?page=' .$this->config->getPage() . '" alt=""/></div></noscript>
';

$this->template->set('statisticsImg', $s_stats);
?>