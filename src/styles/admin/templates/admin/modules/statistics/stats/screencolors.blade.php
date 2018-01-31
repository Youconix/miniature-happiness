@extends layouts/admin.blade.php

@section('body_content')
<section id="statistics">
    <section class="item_header">
       <h1 id="statistics_screencolors_title">{{ $screenColorsTitle }}  {{ $title_startdate }} - {{ $title_enddate }}</h1>
    </section>
    
    <section class="item_body">
         <canvas id="screenColorsCanvas" width="1200" height="600">
         </canvas> 
       
       <script>
       var lines = {!! json_encode($lines) !!};
       var screenColors = {!! json_encode($screenColors) !!};
       
       $(document).ready(() => {
        statistics.screenColors(screenColors, lines);
       })
       </script>
    </section>
</section>
@endsection
