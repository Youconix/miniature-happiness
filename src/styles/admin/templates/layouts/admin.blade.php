<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
        <meta http-equiv="content-type" content="application/xhtml+xml; charset={{ $encoding }}">
    <title>{{ $mainTitle }} {{ $title }}</title>
    <link rel="stylesheet" href="/styles/admin/css/cssPage.css" media="screen">
    <link rel="stylesheet" href="/{{ $shared_style_dir }}css/tabs.css" media="screen">
    <link rel="stylesheet" href="/{{ $shared_style_dir }}css/HTML5_validation.css" media="screen">
    <link rel="stylesheet" href="/{{ $shared_style_dir }}css/animation.css" media="screen">
    <script src="/js/jquery-2.0.3.min.js"></script>
    <script src="/js/admin/admin.js"></script>
    <script src="/js/general.js"></script>
    <script src="/js/tabs.js"></script>
    <script src="/js/validation.js"></script>
    <script src="/js/site.js"></script>
    
    {!! $head !!}
</head>
<body>
<section id="wrapper">
    {!! $noscript !!}
    @include('header_admin.blade.php')
    
    <section id="content">
            @yield('body_content')
    </section>
</section>
</body>
</html>
