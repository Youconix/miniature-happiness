@extends layouts/admin.blade.php

@section('head')
<script src="/admin/modules/settings/js/database.js"></script>
<script src="/js/admin/language.php?lang={{ $currentLanguage }}"></script>
<link rel="stylesheet" href="/admin/modules/settings/settings.css"/>
@endsection

@section('body_content')
<section id="settings">
    <section class="item_header">
       <h1 id="settings_database_title">{{ $databaseTitle }}</h1>
    </section>
    
    <section class="item_body">
        <h2 id="notice" class="notice"></h2>
        
        <form action="path('admin_settings_database_save')" method="post" data-check="path('admin_settings_database_check')" id="database_form">
        <fieldset>
            <label class="label" for="prefix">{{ $prefixText }}</label>
            <input type="text" id="prefix" name="prefix" value="{{ $prefix }}">
        </fieldset>
        <fieldset>
            <label class="label" for="type">{{ $typeText }} *</label>
            <select id="type" name="type">
                @foreach($databases as $choice)
                    <option value="{{ $choice['value'] }}" {!! $choice['selected'] !!}>{{ $choice['text'] }}</option>
                @endforeach
            </select>
        </fieldset>
        <fieldset>
            <label class="label" for="username">{{ $usernameText }} *</label>
            <input type="text" id="username" name="username" value="{{ $username }}" data-validation="{{ $usernameError }}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="password">{{ $passwordText }} *</label>
            <input type="password" id="password" name="password" value="{{ $password }}" data-validation="{{ $passwordError }}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="database">{{ $databaseText }} *</label>
            <input type="text" id="database" name="database" value="{{ $database }}" data-validation="{{ $databaseError }}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="host">{{ $hostText }} *</label>
            <input type="text" id="host" name="host" value="{{ $host }}" data-validation="{{ $hostError }}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="port">{{ $portText }} *</label>
            <input type="number" id="port" name="port" value="{{ $port }}" min="1" step="1" required>
        </fieldset>
        
        <p><input type="button" id="settings_database_save" value="{{ $saveButton }}"></p>
        </form>
    </section>
</section>
@endsection
