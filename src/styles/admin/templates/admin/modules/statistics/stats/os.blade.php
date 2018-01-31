@extends layouts/admin.blade.php

@section('body_content')
<section id="statistics">
    <section class="item_header">
       <h1 id="statistics_os_title">{{ $osTitle }}  {{ $title_startdate }} - {{ $title_enddate }}</h1>
    </section>
    
    <section class="item_body">
         <canvas id="osCanvas" width="1200" height="600">
         </canvas> 
         
         <section id="no_html5">
         <table>
         <thead>
         </thead>
         <tbody>
         
         </tbody>
         </table>
         </section>
       
       <script>
       var lines = {!! json_encode($lines) !!};
       var os = {!! json_encode($os) !!};
       
       $(document).ready(() => {
        statistics.os(os, lines);
       })
       </script>
    </section>
</section>
@endsection
