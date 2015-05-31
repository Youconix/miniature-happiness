<!DOCTYPE html>
<html lang="{lang}">
<head>
        <meta http-equiv="content-type" content="application/xhtml+xml; charset={encoding}">
    <title>{mainTitle} {title}</title>
    <link rel="stylesheet" href="{NIV}{style_dir}css/cssPage.css" media="screen">
    <link rel="stylesheet" href="{NIV}{shared_style_dir}css/tabs.css" media="screen">
    <link rel="stylesheet" href="{NIV}{shared_style_dir}css/HTML5_validation.css" media="screen">
    <script src="{NIV}js/jquery-2.0.3.min.js"></script>
    <script src="{NIV}js/admin/admin.js"></script>
    <script src="{NIV}js/general.js"></script>
    <script src="{NIV}js/tabs.js"></script>
    <script src="{NIV}js/validation.js"></script>
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