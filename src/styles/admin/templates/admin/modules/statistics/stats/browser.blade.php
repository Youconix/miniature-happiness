@extends layouts/admin.blade.php

@section('body_content')
<section id="statistics">
    <section class="item_header">
       <h1 id="statistics_browser_title">{{ $browserTitle }}  {{ $title_startdate }} - {{ $title_enddate }}</h1>
    </section>
    
    <section class="item_body">
         <canvas id="browserCanvas" width="1250" height="600">
         </canvas> 
       
       <script>
       var lines = {!! json_encode($lines) !!};
       var browsers = {!! json_encode($browsers) !!};
       
       $(document).ready(() => {
        statistics.browser(browsers, lines);
       })
       </script>
    </section>
</section>
@endsection
