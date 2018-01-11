@extends layouts/admin.blade.php

@section('body_content')
<section id="statistics">
    <section class="item_header">
       <h1 id="statistics_screensizes_title">{{ $screenSizesTitle }}  {{ $title_startdate }} - {{ $title_enddate }}</h1>
    </section>
    
    <section class="item_body">
         <canvas id="screenSizesCanvas" width="1250" height="600">
         </canvas> 
       
       <script>
       var screenSizes = {!! json_encode($screenSizes) !!};
       var lines = {!! json_encode($lines) !!};
       
       $(document).ready(() => {
        statistics.screenSizes(screenSizes, lines);
       })
       </script>
    </section>
</section>
@endsection
