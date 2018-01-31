@extends layouts/admin.blade.php

@section('body_content')
<section id="statistics">
    <section class="item_header">
       <h1 id="statistics_pages_title">{{ $pagesTitle }}  {{ $title_startdate }} - {{ $title_enddate }}</h1>
    </section>
    
    <section class="item_body">
         <canvas id="pagesCanvas" width="1200" height="600">
         </canvas> 
       
       <script>
       var pages = {!! json_encode($pages) !!};
       var lines = {!! json_encode($lines) !!};
       
       $(document).ready(() => {
        statistics.pages(pages, lines);
       })
       </script>
       </script>
    </section>
</section>
@endsection
