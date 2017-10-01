<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
  <meta http-equiv="content-type" content="application/xhtml+xml; charset={{ $encoding }}">
  <title>{{ $mainTitle }} {{ $title }}</title>
  <link rel="stylesheet" href="/combiner/css/{{ $style_dir }}css/cssPage.css,{{ $shared_style_dir }}css/animation.css">
  <script src="/combiner/javascript/js/jquery-2.0.3.min.js,js/general.js,js/site.js"></script>
  {!! $head !!}
</head>
<body {{ $autostart }}>
<section id="wrapper">
  {!! $noscript !!}
  @include('header.blade.php')
    
  @include('menu.blade.php')

  <section id="content">
    @yield('body_content')
  </section>

  @include('footer.blade.php')

  @if( isset($statisticsImg) )
  {!! $statisticsImg !!}
  @endif
</section>
</body>
</html>