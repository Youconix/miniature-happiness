<!DOCTYPE html>
<html lang="{lang}">
<head>
        <meta http-equiv="content-type" content="application/xhtml+xml; charset={encoding}">
    <title>{mainTitle} {title}</title>
    <link rel="stylesheet" href="{NIV}{style_dir}css/cssPage.css" media="screen">
    <link rel="stylesheet" href="{NIV}{style_dir}css/admin/cssAdmin.css" media="screen">
    <link rel="stylesheet" href="{NIV}{style_dir}css/tabs.css" media="screen">
    <script src="{NIV}js/jquery-2.0.3.min.js"></script>
    <script src="{NIV}js/general.js"></script>
    <script src="{NIV}js/tabs.js"></script>
    {headblock}
</head>
<body {autostart}>
<section id="wrapper">
    {noscript}
    <include src="menu_admin.tpl">
    
    <section id="content">
            {body_content}
    </section>
</section>
</body>
</html>