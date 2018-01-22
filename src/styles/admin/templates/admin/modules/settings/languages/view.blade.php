@extends layouts/admin.blade.php

@section('body_content')
<section id="settings">
    <section class="item_header">
       <h1 id="settings_language_title">{{ $languageTitle }}</h1>
    </section>
    
    <section class="item_body">
        <h2 id="notice" class="notice"></h2>
        
        <form action="path('admin_settings_language_save')" method="post" id="language_form">
        <fieldset>
            <label class="label" for="default_language">{{ $defaultLanguageText }}</label>
            <select name="default_language">
            @foreach($languages as $defaultLanguage)
                <option value="{{ $defaultLanguage['value'] }}" {!! $defaultLanguage['selected'] !!}>{{ $defaultLanguage['text'] }}</option>
            @endforeach
            </select>
        </fieldset>
        <fieldset>
            <label class="label" for="backup_language">{{ $backupLanguageText }}</label>
            <select name="backup_language">
            @foreach($backupLanguages as $backupLanguage)
                <option value="{{ $backupLanguage['value'] }}" {!! $backupLanguage['selected'] !!}>{{ $backupLanguage['text'] }}</option>
            @endforeach
            </select>
        </fieldset>
        <p><input type="button" id="settings_language_save" value="{{ $saveButton }}"></p>
        </form>
    </section>
</section>

<script>
<!--
$(document).ready(() => {
    settingsLanguage.init();
});
//-->
</script>
@endsection
