<header id="header_admin">
    <h1>Control panel</h1>
    
    <nav>
        <ul>
            @foreach($headerLanguage AS $language)
                <li class="language_icon"><a href="javascript:switchLanguage('{{ $language['code'] }}')"><img 
src="/{{ $shared_style_dir }}images/flags/{{ $language['code'] }}.png" alt="{{ $language['language'] }}" title="{{ $language['language'] }}" 
class="icon"></a></li> 
            @endforeach
            <li><a href="/">{{ $close }}</a></li>
            <li><a href="/logout/index">{{ $logout }}</a></li>
            <li id="admin_menu_link">{{ $adminMenuLink }}</li>
            <li>{{ $loginHeader }}</li>
        </ul>
    </nav>
    
    <script>
    <!--
    function switchLanguage(language){
        $.get("/index.php?lang="+language,function(){
            location.reload();
        });
    }
    //-->
    </script>
</header>
