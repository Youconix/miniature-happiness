@extends layouts/admin.blade.php

@section('body_content')
<section id="settings">
    <section class="item_header">
       <h1 id="settings_no_cache_title">{{ $cacheTitle }}</h1>
    </section>
    
    <section class="item_body">
        <h2 id="notice" class="notice"></h2>
        
        <h3>{{ $excludeTitle }}</h3>
        
        <form action="path('admin_settings_no_cache')" method="post" id="no_cache_form">
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
        </form>
        
        <h3>{{ $addExcludeTitle }}</h3>
        <table id="nonCacheList" data-styledir="/{{ $shared_style_dir }}" data-delete="{{ $delete }}" data-path="path('admin_settings_cache_delete')">
        <tbody>
        @foreach( $noCache as $item)
            <tr data-id="{{ $item['id'] }}" data-name="{{ $item['name'] }}">
                <td><img src="/{{ $shared_style_dir }}images/icons/delete.png" alt="{{ $delete }}" title="{{ $delete }}"></td>
                <td>{{ $item['name'] }}</td>
            </tr>
        @endforeach
        </tbody>
        </table>
    </section>
</section>

<script>
<!--
$(document).ready(() => {
    settingsNoCache.init();
});
//-->
</script>
@endsection
