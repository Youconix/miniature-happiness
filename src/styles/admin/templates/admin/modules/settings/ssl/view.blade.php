@extends layouts/admin.blade.php

@section('body_content')

<section id="settings">
    <section class="item_header">
       <h1 id="settings_ssl_title">{{ $sslTitle }}</h1>
    </section>
    
    <section class="item_body">        
        <h2 id="notice" class="notice"></h2>
        
        <form action="path('admin_settings_ssl_save')" method="post" id="ssl_form">
        <fieldset>
            <label class="label">{{ $noSslText }}</label>
            <input type="radio" name="ssl" value="{{ $no_ssl }}" {!! $no_ssl_value !!}>
        </fieldset>
        <fieldset>
            <label class="label">{{ $loginSslText }}</label>
            <input type="radio" name="ssl" value="{{ $login_ssl }}" {!! $login_ssl_value !!}>
        </fieldset>
        <fieldset>
            <label class="label">{{ $alwaysSslText }}</label>
            <input type="radio" name="ssl" value="{{ $always_ssl }}" {!! $always_ssl_value !!}>
        </fieldset>
        
        <p><input type="button" id="settings_ssl_save" value="{{ $saveButton }}"></p>
        
        <input type="hidden" id="current_ssl" value="{{ $current_ssl }}">
        </form>
    </section>
</section>

<script>
<!--
$(document).ready(() => {
    settingsSSL.init();
});
//-->
</script>
@endsection
