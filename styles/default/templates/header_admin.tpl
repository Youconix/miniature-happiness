<header id="header_admin">
    <h1>Control panel</h1>
    
    <nav>
        <ul>
            <block {header_languages}>
                <li class="language_icon"><a href="javascript:switchLanguage('{code}')"><img src="{NIV}{style_dir}images/flags/{code}.png" alt="{language}" title="{language}" class="icon"></a></li> 
            </block>
            <li><a href="{NIV}">{close}</a></li>
            <li><a href="{NIV}logout/index">{logout}</a></li>
            <li id="admin_menu_link">{adminMenuLink}</li>
            <li>{loginHeader}</li>
        </ul>
    </nav>
    
    <script>
    <!--
    function switchLanguage(language){
        $.get("{LEVEL}index.php?lang="+language,function(){
            location.reload();
        });
    }
    //-->
    </script>
</header>