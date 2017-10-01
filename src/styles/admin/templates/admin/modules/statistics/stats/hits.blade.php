<section id="hits">
    <section class="item_header">
       <h1 id="statistics_hits_title">{{ $hitsTitle }} {{ $title_startdate }} - {{ $title_enddate }}</h1>
    </section>
    
    <section class="item_body">
         <canvas id="hitsCanvas" width="1000" height="400">
         </canvas> 
         
         <section id="no_html5">
         <table>
         <thead>
         <tr>
            <td><br></td>
            @foreach(@labels AS $label)
            <td>{{ $label }}</td>
            @endforeach
         </tr>
         </thead>
         <tbody>
         <tr>
            <td>{{ $lines[0]['text'] }}</td>
            @foreach($hits[0] AS $visitors)
            <td style="color:#{{ $lines[0]['color'] }}">{{ $visitors }}</td>
            @endforeach
         </tr>
         <tr>
            <td>{{ $lines[1]['text'] }}</td>
         </tr>
         <tr>
            <td>{{ $lines[2]['text'] }}</td>
         </tr>
         </tbody>
         </table>
         </section>
       
       <script>
       var lines = {!! lines !!};
       var labels = {!! labels !!};
       var hits = {!! hits !!};
       </script>
    </section>
</section>
