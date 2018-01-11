@extends layouts/admin.blade.php

@section('head')
<script src="/admin/modules/settings/js/general.js"></script>
<script src="/js/admin/language.php?lang={{ $currentLanguage }}"></script>
<link rel="stylesheet" href="/admin/modules/settings/settings.css"/>
@endsection

@section('body_content')
<section id="settings">
    <section class="item_header">
       <h1 id="settings_general_title">{{ $generalTitle }}</h1>
    </section>
    
    <section class="item_body"> 
        <form action="path('admin_settings_general_save')" method="post" id="general_form">
        <h2 id="notice" class="notice"></h2>
        
        <fieldset>
            <label class="label" for="name_site">{{ $nameSiteText }} *</label>
            <input type="text" id="name_site" name="name_site" value="{{ $nameSite }}" data-validation="{{ $nameSiteError }}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="site_url">{{ $siteUrlText }} *</label>
            <input type="text" id="site_url" name="site_url" value="{{ $siteUrl }}" data-validation="{{ $siteUrlError }}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="timezone">{{ $timezoneText }} *</label>
            <input type="text" id="timezone" name="timezone" value="{{ $timezone }}" pattern="^[A-Z]{$1}[a-z]+/{$1}[A-Z]{$1}[a-z]+$" 
                data-validation="{{ $timezoneError }}" required>
        </fieldset>
        <fieldset><br></fieldset>
        <fieldset>
            <label class="label" for="template">{{ $templateText }} *</label>
            <select id="template" name="template">
            @foreach($template as $item)
                <option value="{{ $item['value'] }}" {!! $item['selected'] !!}>{{ $item['text'] }}</option>
            @endforeach
            </select>
        </fieldset>
        <fieldset><br></fieldset>
        <fieldset>
            <label class="label" for="logger">{{ $loggerText }} *</label>
            <input type="text" list="logger_list" id="logger" name="logger" value="{{ $logger }}" data-validation="{{ $loggerError }}" required>
            <datalist id="logger_list">
            <option label="Default" value="default">
            <option label="Syslog" value="syslog">
            <option label="Errorlog" value="errorlog">
            </datalist>
        </fieldset>
        <div id="location_log_default" {{ $location_log_default }}>
        <fieldset>
            <label class="label" for="log_location">{{ $logLocationText }} *</label>
            <input type="text" id="log_location" name="log_location" value="{{ $logLocation }}" data-validation="{{ $logLocationError }}" required>
        </fieldset>
        </div>
        <fieldset>
            <label class="label" for="log_size">{{ $logSizeText }} *</label>
            <input type="number" id="log_size" name="log_size" value="{{ $logSize }}" min="1000" step="1000" data-validation="{{ $logSizeError }}" required>
        </fieldset>
        
        <p><input type="button" id="settings_general_save" value="{{ $saveButton }}"></p>
        </form>
    </section>
</section>
@endsection
