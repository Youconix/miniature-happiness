<!DOCTYPE html>
<html lang="{lang}">
<head>
	<meta http-equiv="content-type" content="application/xhtml+xml; charset={encoding}">
    <title>{title}</title>
    {headblock}
    <link rel="stylesheet" href="{styledir}css/install.css">
    <script src="/js/jquery-2.0.3.min.js"></script>
    <script src="/js/general.js"></script>
    <script src="/js/install.js"></script>
    <script src="/js/animation.js"></script>
    <script src="/js/validation.js"></script>
</head>
<body {autostart} data-styledir="{styledir}" data-datadir="{datadir}">
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
