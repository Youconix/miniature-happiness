@extends layouts/admin.blade.php

@section('body_content')
<section id="settings">
    <section class="item_header">
       <h1 id="settings_login_title">{{ $generalTitle }}</h1>
    </section>
    
    <section class="item_body">        
        <h2 id="notice" class="notice"></h2>
        
        <h2>Settings</h2>
        <form id="login_settings" action="path('admin_settings_login_save')" method="post">
       <!-- login redirects -->
       <fieldset>
            <label class="label" for="login_redirect">{{ $loginRedirectText }} *</label>
            <input type="text" name="login_redirect" value="{{ $loginRedirect }}" data-validation="{{ $redirectError }}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="logout_redirect">{{ $logoutRedirectText }} *</label>
            <input type="text" name="logout_redirect" value="{{ $logoutRedirect }}" data-validation="{{ $redirectError }}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="registration_redirect">{{ $registrationRedirectText }} *</label>
            <input type="text" name="registration_redirect" value="{{ $registrationRedirect }}" data-validation="{{ $redirectError }}" required>
        </fieldset>
        <fieldset>
            <label class="label" for="default_guard">{{ $defaultGuardText }}</label>
            <select name="default_guard">
            
            </select>
        </fieldset>
        <fieldset>
            <label class="label" for="registration_enabled">{{ $registrationEnabledText }}</label>
            {!! $registrationEnabled->generate() !!}
        </fieldset>
        <fieldset></fieldset>
        
        <h2>Guards **</h2>
        @foreach($guards as $name => $guard)
        <fieldset>
            <label class="label">{{ $guard[0]->getDisplayName() }}</label>
            {!! $guard[1]->generate() !!}
            
            @if($guard[0]->hasConfig())
                <div class="login_config" id="{{ $guard[0]->getName() }}_config">
                    {!! $guard[0]->getConfigForm() !!}
                </div>
            @endif
        </fieldset>
        <input type="hidden" name="guard[]" value="{{ $name }}">
        @endforeach
        
        <h5>** {{ $loginChoiceText }}</h5>
        
        <p><input type="button" id="settings_login_save" value="{{ $saveButton }}"></p>
        </form>
    </section>
</section>

<script>
<!--
    let guards = {!! $enabledGuards !!};
    $(document).ready(() => {
        settingsLogin.init(guards);
    });
//-->
</script>
@endsection
