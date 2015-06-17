<!DOCTYPE html>
<html lang="{lang}">
<head>
		<meta http-equiv="content-type" content="application/xhtml+xml; charset={encoding}">
    <title>{mainTitle} {title}</title>
    <link rel="stylesheet" href="{NIV}{style_dir}css/cssPage.css" media="screen">
    <link rel="stylesheet" href="{NIV}{shared_style_dir}css/animation.css">
    <script src="{NIV}js/jquery-2.0.3.min.js"></script>
    <script src="{NIV}js/general.js"></script>
    {headblock}
</head>
<body {autostart}>
<section id="wrapper">
	{noscript}
    <include src="header.tpl">
    
    <include src="menu.tpl">

    <section id="content">
	        {body_content}
    </section>

    <include src="footer.tpl">

    {statisticsImg}
</section>
</body>
</html>