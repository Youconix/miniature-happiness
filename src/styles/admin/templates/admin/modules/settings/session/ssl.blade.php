@extends layouts/admin.blade.php

@section('head')
<script src="/admin/modules/settings/js/session.js"></script>
<script src="/js/admin/language.php?lang={{ $currentLanguage }}"></script>
<link rel="stylesheet" href="/admin/modules/settings/settings.css"/>
@endsection

@section('body_content')
<section id="settings">
    <section class="item_header">
       <h1 id="settings_sessions_title">{{ $generalTitle }}</h1>
    </section>
    
    <section class="item_body">        
        <h2 id="notice" class="notice"></h2>
        
        <form action="path('admin_settings_ssl_save')" method="post" id="ssl_form">
        <fieldset>
            <label class="label" for="session_name">{{ $sessionNameText }} *</label>
            <input type="text" id="session_name" name="session_name" value="{{ $sessionName }}" data-validation="{{ $sessionNameError }}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="session_path">{{ $sessionPathText }} *</label>
            <input type="text" id="session_path" name="session_path" value="{{ $sessionPath }}" data-validation="{{ $sessionPathError }}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="session_expire">{{ $sessionExpireText }} *</label>
            <input type="number" id="session_expire" name="session_expire" min="60" step="1" data-validation-min="{{ $sessionExpireError }}" value="{{ $sessionExpire }}" required>
        </fieldset>
        
        <p><input type="button" id="settings_sessions_save" value="{{ $saveButton }}"></p>
        </form>
    </section>
</section>
@endsection
