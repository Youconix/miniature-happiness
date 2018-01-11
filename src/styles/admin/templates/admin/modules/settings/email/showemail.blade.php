@extends layouts/admin.blade.php

@section('head')
<script src="/admin/modules/settings/js/email.js"></script>
<script src="/js/admin/language.php?lang={{ $currentLanguage }}"></script>
<link rel="stylesheet" href="/admin/modules/settings/settings.css"/>
@endsection

@section('body_content')
<section id="settings">
    <section class="item_header">
       <h1 id="settings_email_title">{{ $emailTitle }}</h1>
    </section>
    
    <section class="item_body">
        <h2>{{ $emailGeneralTitle }}</h2>
        
        <h2 id="notice" class="notice"></h2>
        
        <form action="path('admin_settings_email_save')" method="post" id="email_form">
        <fieldset>
            <label class="label" for="email_name">{{ $nameText }} *</label>
            <input type="text" id="email_name" name="email_name" value="{{ $name }}" data-validation="{{ $nameError }}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="email_email">{{ $emailText }} *</label>
            <input type="text" id="email_email" name="email_email" value="{{ $email }}" data-validation="{{ $emailError }}" required>
        </fieldset>
        
        
        <h2>{{ $SmtpTitle }}</h2>
        <fieldset>
            <label class="label" for="smtp_active">{{ $smtpActiveText }}</label>
            <input type="checkbox" id="smtp_active" name="smtp_active" @if( $smtpActive ) checked="checked" @endif value="1">
        </fieldset>
        <div id="smtp_settings" @if( !$smtpActive ) style="display:none" @endif>
        <fieldset>
            <label class="label" for="smtp_host">{{ $smtpHostText }} *</label>
            <input type="text" id="smtp_host" name="smtp_host" value="{{ $smtpHost }}" data-validation="{{ $smtpHostError }}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="smtp_username">{{ $smtpUsernameText }} *</label>
            <input type="text" id="smtp_username" name="smtp_username" value="{{ $smtpUsername }}" data-validation="{{ $smtpUsernameError }}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="smtp_password">{{ $smtpPasswordText }} *</label>
            <input type="password" id="smtp_password" name="smtp_password" value="{{ $smtpPassword }}" data-validation="{{ $smptPasswordError }}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="smtp_port">{{ $smtpPortText }} *</label>
            <input type="number" id="smtp_port" name="smtp_port" value="{{ $smtpPort }}" min="1" step="1" data-validation="{{ $smtpPortError }}" required>
        </fieldset>
        </div>
        
        
        <h2>{{ $emailAdminTitle }}</h2>
        
        <fieldset>
            <label class="label" for="email_admin_name">{{ $nameAdminText }} *</label>
            <input type="text" id="email_admin_name" name="email_admin_name" value="{{ $nameAdmin }}" data-validation="{{ $nameError }}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="email_admin_email">{{ $emailAdminText }} *</label>
            <input type="text" id="email_admin_email" name="email_admin_email" value="{{ $emailAdmin }}" data-validation="{{ $emailError }}" required>
        </fieldset>
        
        <p><input type="button" id="settings_email_save" value="{{ $saveButton }}"></p>
        </form>
    </section>
</section>
@endsection
