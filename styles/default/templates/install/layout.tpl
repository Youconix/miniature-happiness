<!DOCTYPE html>
<html lang="{lang}">
<head>
	<meta http-equiv="content-type" content="application/xhtml+xml; charset={encoding}">
    <title>{title}</title>
    {headblock}
    <link rel="stylesheet" href="{LEVEL}{styledir}css/install.css">
    <script src="{LEVEL}/js/jquery-2.0.3.min.js"></script>
    <script src="{LEVEL}/js/general.js"></script>
    <script src="{LEVEL}/js/install.js"></script>
    <script src="{LEVEL}/js/animation.js"></script>
    <script src="{LEVEL}/js/validation.js"></script>
</head>
<body {autostart} data-styledir="{LEVEL}{styledir}" data-datadir="{datadir}">
<section class="container">
	<header class="holder">Scripthulp framework {version}</header>
	
	<section id="content">
		<ul id="progressBar">
			{progress}
		</ul>
		
		<section>	
			{content}
		</section>
	</section>
	
	<footer class="holder"></footer>
</section>
<input type="hidden" id="step" value="{step}">
</body>
</html>
