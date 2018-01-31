@extends layouts/admin.blade.php

@section('body_content')
<section id="statistics">
    <section class="item_header">
       <h1 id="statistics_hits_title">{{ $hitsTitle }} {{ $title_startdate }} - {{ $title_enddate }}</h1>
    </section>
    
    <section class="item_body">
         <canvas id="hitsCanvas" width="1200" height="600">
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
            @foreach($visitors['visitors'] AS $visitor)
            <td style="color:#{{ $color1 }}">{!! number_format($visitor, 0, ',', ' ') !!}</td>
            @endforeach
         </tr>
         <tr>
            <td>{{ $line2 }}</td>
            @foreach($visitors['unique'] AS $visitor)
            <td style="color:#{{ $color2 }}">{!! number_format($visitor, 0, ',', ' ') !!}</td>
            @endforeach
         </tr>
         <tr>
            <td>{{ $line3 }}</td>
            @foreach($visitors['pages'] AS $visitor)
            <td style="color:#{{ $color3 }}">{!! number_format($visitor, 0, ',', ' ') !!}</td>
            @endforeach
         </tr>
         </tbody>
         </table>
         </section>
       
       <script>
       var labels = {!! json_encode($labels) !!};
       var lines = [{"color" : "{{ $color1 }}", "text" : "{{ $line1 }}"},
            {"color" : "{{ $color2 }}", "text" : "{{ $line2 }}"},
            {"color" : "{{ $color3 }}", "text" : "{{ $line3 }}"}
        ];
       var visitors = [
            {!! json_encode($visitors['visitors']) !!},
            {!! json_encode($visitors['unique']) !!},
            {!! json_encode($visitors['pages']) !!}
        ];
       
       $(document).ready(function() {
            statistics.visitors(visitors, lines, labels);
        });
       </script>
    </section>
</section>
@endsection
