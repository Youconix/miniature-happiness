@extends layouts/admin.blade.php

@section('head')
<script src="/admin/modules/settings/js/cache.js"></script>
<script src="/js/admin/language.php?lang={{ $currentLanguage }}"></script>
<link rel="stylesheet" href="/admin/modules/settings/settings.css"/>
@endsection

@section('body_content')
<section id="settings">
    <section class="item_header">
       <h1 id="settings_cache_title">{{ $cacheTitle }}</h1>
    </section>
    
    <section class="item_body">
        <h2 id="notice" class="notice"></h2>
        
        <form action="path('')" method="post">
        <fieldset>
	  <label class="label" for="cacheActive">{{ $cacheActiveText }}</label>
	  <input type="checkbox" id="cacheActive" name="cacheActive" {{ $cacheActive }}>
        </fieldset>
        <div id="cacheSettings" {{ $cacheSettings }}>
            <fieldset>
                <label class="label" for="expire">{{ $cacheExpireText }} *</label>
                <input type="number" id="expire" name="expire" min="60" step="1" value="{{ $cacheExpire }}" data-validation="{{ $expireError }}" required>
            </fieldset>
        </div>
        
        <p><input type="button" id="settings_cache_save" value="{{ $saveButton }}"></p>
        </form>
        
        <h4>{{ $excludedCachingTitle }}</h4>
        
        <table id="nonCacheList" data-styledir="{{ $NIV }}{{ $shared_style_dir }}" data-delete="{{ $delete }}">
        <tbody>
        @foreach( $noCache as $item)
            <tr data-id="{{ $item['id'] }}">
                <td><img src="{{ $NIV }}{{ $shared_style_dir }}images/icons/delete.png" alt="{{ $delete }}" title="{{ $delete }}"></td>
                <td>{{ $item['name'] }}</td>
            </tr>
        @endforeach
        </tbody>
        </table>
        
        <fieldset>
            <select id="noCachePage">
                <option value="">{{ $page }}</option>
                @foreach($addresses as $address)
                    <option value="{{ $address }}">{{ $address }}</option>
                @endforeach
            </select>
        </fieldset>
        <fieldset>
	  <input type="button" id="no_cache_submit" value="{{ $addButton }}">
        </fieldset>
    </section>
</section>
@endsection
