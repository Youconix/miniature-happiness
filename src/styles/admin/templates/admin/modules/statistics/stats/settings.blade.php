@extends layouts/admin.blade.php

@section('body_content')
<section id="statistics">
    <section class="item_header">
       <h1 id="statistics_hits_title">{{ $settingsTitle }}</h1>
    </section>
    
    <section class="item_body">
        @if(isset($settingsSaved))
        <h2 class="Notice">{!! $settingsSaved !!}</h2>
        @endif
    
        <form action="path('admin_statistics_settings_save')" method="post">
        <fieldset>
            <label class="label" for="enabled">{{ $enabledText }} *</label>
            {!! $enabled->generate() !!}
        </fieldset>
        <fieldset>
            <label class="label" for="ignore">{!! $ignoreText !!} *</label>
        </fieldset>
        <fieldset>
            <textarea id="ignore" name="ignore">{{ $ignore }}</textarea>
        </fieldset>
        
        <p><input type="submit" id="statistics_save_button" value="{{ $saveButton }}"></p>
         </section>
         </form>
    </section>
</section>
@endsection
