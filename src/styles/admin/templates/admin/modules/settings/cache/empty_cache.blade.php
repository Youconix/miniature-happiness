@extends layouts/admin.blade.php

@section('body_content')
<section id="settings">
    <section class="item_header">
       <h1 id="settings_cache_title">{{ $cacheTitle }}</h1>
    </section>
    
    <section class="item_body">        
        <form action="path('admin_settings_cache_empty')" method="post" id="remove_caches">
            <h2 id="delete_process">{{ $cacheRemovalProcess }}</h2>
            
            <h2 id="delete_done">{{ $cacheRemovalComplete }}</h2>
        
            <p><input type="button" id="settings_cache_empty" value="{{ $emptyButton }}"></p>
        </form>
    </section>
</section>

<script>
<!--
$(document).ready(() => {
console.debug('lalallaa');
  settingsEmptyCache.init();
});
//-->
</script>
@endsection
