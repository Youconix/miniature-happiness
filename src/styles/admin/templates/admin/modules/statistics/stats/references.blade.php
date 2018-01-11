@extends layouts/admin.blade.php

@section('body_content')
<section id="statistics">
    <section class="item_header">
       <h1 id="statistics_references_title">{{ $referencesTitle }}  {{ $title_startdate }} - {{ $title_enddate }}</h1>
    </section>
    
    <section class="item_body">
         <canvas id="referencesCanvas" width="1250" height="600">
         </canvas> 
       
       <script>
       var references = {!! json_encode($references) !!};
       var lines = {!! json_encode($lines) !!};
       
       $(document).ready(() => {
        statistics.references(references, lines);
       })
       </script>
    </section>
</section>
@endsection
