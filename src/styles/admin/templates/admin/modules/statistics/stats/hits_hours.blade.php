@extends layouts/admin.blade.php

@section('body_content')
<section id="statistics">
    <section class="item_header">
       <h1 id="statistics_hits_title">{{ $hitsTitle }}  {{ $title_startdate }} - {{ $title_enddate }}</h1>
    </section>
    
    <section class="item_body">
         <canvas id="hitsCanvas" width="1250" height="600">
         </canvas> 
         
         <section id="no_html5">
         <table>
         <thead>
         <tr>
            <td><br></td>
                @foreach($labels AS $label)
                <td>{{ $label }}</td>
            @endforeach
         </tr>
         </thead>
         <tbody>
         <tr>
            <td>{{ $line1 }}</td>
            @foreach($hits[0] AS $visitors)
            <td style="color:#{{ $color1 }}">{!! number_format($visitors, 0, ',', ' ') !!}</td>
            @endforeach
         </tr>
         </tbody>
         </table>
         </section>
       
       <script>
       var lines = [{"color" : "{{ $color1 }}", "text" : "{{ $line1 }}"}];
       var labels = {!! json_encode($labels) !!};
       var hits = {!! json_encode($hits) !!};
       
       $(document).ready(function() {
            statistics.hitsHour(hits, lines, labels);
        });
       </script>
    </section>
</section>
@endsection
