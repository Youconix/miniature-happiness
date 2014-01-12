<!DOCTYPE html>
<html lang="{lang}">
<head>
		<meta http-equiv="content-type" content="application/xhtml+xml; charset={encoding}">
    <title>{title}</title>
    {headblock}
    <style>
    <!--
    body {	margin:0; padding:0; }
    .container {
        width:99.9%;
    }
    header {	width:1000px;	margin:auto; margin-top:10%; height:25px;}
    #content {	
    	width:972px;	margin:auto; height:auto; min-height:200px; padding-top:10px; padding-bottom:10px; 
    	border-left:4px solid #200000; border-right:4px solid #200000; background-color:#8f8e21; -webkit-linear-gradient(top, #8f8e21, #b1b036); 
    	background: -moz-linear-gradient(top, #8f8e21, #b1b036); background: -ms-linear-gradient(top, #8f8e21, #b1b036); background: linear-gradient(top, #8f8e21, #b1b036); 
    	opacity:0.5; filter:alpha(opacity=50); /* For IE8 and earlier */}
    #content>div {	font-weight:bold; opacity:1.0; filter:alpha(opacity=100); /* For IE8 and earlier */}
    footer { width:1000px;	margin:auto; height:25px;}
    .holder {  border-radius: 20px; ms-border-radius: 20px;  background-color:#471414; -webkit-linear-gradient(top, #471414, #983f3f); 
    	background: -moz-linear-gradient(top, #471414, #983f3f); background: -ms-linear-gradient(top, #471414, #983f3f); background: linear-gradient(top, #471414, #983f3f); }
    .container table {
        width:500px;
        margin:auto;
    }
    h1, h2, p {
        text-align:center;
    }
    .button, a.button {padding:3px; background-color:black;color:white; text-decoration:none; border-radius:25px; ms-border-radius:25px; padding:5px;}
    .bold {  font-weight:bold;   }
    .errorNotice {color:#9a0012; }
    .Notice { color:#006600; }
    .title { font-size:1.2em; font-weight:bold; padding-top:3px; padding-bottom:3px;}
    li { list-style-type:none; }
    
    #progressBar { width:99%; display:block; float:left; padding:0; margin:0;  padding-left:5px; margin-bottom:15px; }
    #progressBar li { 	padding:0; margin:0; padding:3px; border-radius:20px; ms-border-radius:20px; 
    	display:block; margin-right:5%; width:20px; text-align:center; float:left;
    }
    .current {	background-color:green; color:#000;  }
    .grey { background-color:#aeaeaa; color:939393; }
    //-->
    </style>
</head>
<body {autostart}>
<section class="container">
	<header class="holder"></header>
	
	<section id="content">
		<div>
			<ul id="progressBar">
				{progress}
			</ul>
			
			{content}
		</div>
	</div>
	
	<footer class="holder"></footer>
</section>
</body>
</html>
