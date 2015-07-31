<!DOCTYPE html>
<html lang="{lang}">
<head>
        <meta http-equiv="content-type" content="application/xhtml+xml; charset={encoding}">
    <title>{mainTitle} {title}</title>
    <link rel="stylesheet" href="/combiner/css/{shared_style_dir}css;tabs.css;HTML5_validation.css;css/animation.css,{style_dir}css/cssPage.css" media="screen">
    <script src="/combiner/javascript//js;jquery-2.0.3.min.js;admin/admin.js;general.js;tabs.js;validation.js;site.js"></script>
    {headblock}
</head>
<body {autostart}>
<section id="wrapper">
    {noscript}
    <include src="header_admin.tpl">
    
    <section id="content">
            {body_content}
    </section>
</section>

<section id="menu_wrapper">
    <include src="menu_admin.tpl">
</section>
</body>
</html>