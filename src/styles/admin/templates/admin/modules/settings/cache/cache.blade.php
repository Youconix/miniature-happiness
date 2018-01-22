@extends layouts/admin.blade.php

@section('body_content')
<section id="settings">
    <section class="item_header">
       <h1 id="settings_cache_title">{{ $cacheTitle }}</h1>
    </section>
    
    <section class="item_body">
        <h2 id="notice" class="notice"></h2>
        
        <form action="path('admin_settings_cache_save')" method="post" id="cache_form">
        <fieldset>
	  <label class="label" for="cacheActive">{{ $cacheActiveText }}</label>
	  {!! $cacheActive->generate() !!}
        </fieldset>
        <div id="cacheSettings">
            <fieldset>
                <label class="label" for="expire">{{ $cacheExpireText }} *</label>
                <input type="number" id="expire" name="expire" min="60" step="1" value="{{ $cacheExpire }}" data-validation="{{ $expireError }}" required>
            </fieldset>
        </div>
        
        <p><input type="button" id="settings_cache_save" value="{{ $saveButton }}"></p>
        </form>
    </section>
</section>

<script>
<!--
$(document).ready(() => {
    settingsCache.init();
});
//-->
</script>
@endsection
