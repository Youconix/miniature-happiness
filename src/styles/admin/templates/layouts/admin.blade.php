<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
    <meta http-equiv="content-type" content="application/xhtml+xml; charset={{ $encoding }}">
    <title>{{ $mainTitle }} {{ $title }}</title>    
    <link rel="stylesheet" href="/resources/css/youconix.css" media="screen">
    <link rel="stylesheet" href="/resources/css/widgets.css" media="screen">
    <link rel="stylesheet" href="/resources/css/controlpanel.css" media="screen">
    <link rel="stylesheet" href="/resources/css/controlpanel_modules.css" media="screen">
    <script src="/resources/js/youconix.min.js"></script>    
    <script src="/resources/js/widgets.min.js"></script>
    <script src="/resources/js/graph.min.js"></script>
    <script src="/resources/js/controlpanel.min.js"></script>    
    <script src="/js/language_admin.php?lang={{ $currentLanguage }}"></script>
    
    {!! $head !!}
    
    @yield('head')
    <script>
    <!--
    var tabs = new Tabs();
    $(document).ready(function(){
     admin.init();
     tabs.init({'id':'menu_wrapper'});
    });
    //-->
    </script>
</head>
<body>
<section id="wrapper">
    {!! $noscript !!}
    @include('header_admin.blade.php')
    
    <section id="content">
	@include('menu_admin.blade.php')
        <section id="admin_panel">
            <section id="adminContent">
                @yield('body_content')
            </section>
        </section>
            
    </section>
</section>
</body>
</html>
